<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\ProspectWeeklyUpdate;
use App\Models\Todo;
use App\Models\TodoLink;
use App\Models\TodoLockSetting;
use App\Models\TodoPdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TodoController extends Controller
{
    private const PLATFORMS = [
        ['key' => 'instagram',   'label' => 'Instagram'],
        ['key' => 'tiktok',      'label' => 'TikTok'],
        ['key' => 'facebook',    'label' => 'Facebook'],
        ['key' => 'snack_video', 'label' => 'Other Video'],
    ];

    private const PDF_TASKS = [
        2 => 'Broadcast & Komentar Sosial Media',
        3 => 'Mengiklankan Akun Instagram',
        4 => 'DM Brosur (Pak Sabar, Pak Henry, Marketing)',
        5 => 'Menyapa & Follow Up Klien Lama',
        6 => 'Memaparkan Rencana Penjualan',
    ];

    public function daily(Request $request): View
    {
        $this->assertMarketing($request);

        $lockSetting = TodoLockSetting::instance();
        $date = $request->filled('date') ? Carbon::parse($request->string('date')) : now();
        $todo = $this->getOrCreateTodo($request, $date);

        $linksByKey = $todo->links()->get()->groupBy('platform')->map->keyBy('slot');

        // Calendar data: per day completion within current month
        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth();

        // Rentang Senin-Minggu diperluas untuk deteksi catatan Tugas 7 mingguan
        $queryStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $queryEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        $todos = Todo::with(['links', 'pdfs'])
            ->where('user_id', $request->user()->id)
            ->whereBetween('date', [$queryStart, $queryEnd])
            ->get();

        // Ambil prospek milik marketing ini pada bulan berjalan
        $monthProspects = Prospect::with([
            'service', 'sender', 'source', 'group', 'status',
        ])
            ->where('marketing_user_id', $request->user()->id)
            ->whereMonth('entry_date', $date->month)
            ->whereYear('entry_date', $date->year)
            ->orderBy('entry_date', 'asc')
            ->get();

        $monthProspectIds = $monthProspects->pluck('id');

        // Ambil seluruh catatan perkembangan prospek target
        $allWeeklyUpdates = ProspectWeeklyUpdate::whereIn('prospect_id', $monthProspectIds)
            ->whereNotNull('note')
            ->where('note', '!=', '')
            ->get()
            ->groupBy('prospect_id');

        // Map status update per prospek (di tabel prospects.note atau prospect_weekly_updates)
        $updatedProspectMap = [];
        foreach ($monthProspects as $tp) {
            $latestUpdate = $allWeeklyUpdates->get($tp->id)?->sortByDesc('updated_at')->first();
            $note = trim((string) ($latestUpdate?->note ?? $tp->note ?? ''));
            if ($note !== '') {
                $updatedProspectMap[$tp->id] = [
                    'note' => $note,
                    'updated_at' => $latestUpdate?->updated_at ?? $tp->updated_at,
                ];
            }
        }

        $userProspectsByDate = $monthProspects->groupBy(fn ($p) => $p->entry_date ? $p->entry_date->format('Y-m-d') : '');

        $todosByDate = $todos->keyBy(fn ($t) => $t->date->format('Y-m-d'));

        $pdfTaskKeys = array_keys(self::PDF_TASKS); // [2,3,4,5,6]
        $totalTasks = 7; // 1 link task + 5 PDF tasks + 1 note task
        $monthStatus = [];

        // Hitung status setiap hari dalam bulan ini
        for ($d = 1; $d <= $date->daysInMonth; $d++) {
            $dayCarbon = $monthStart->copy()->day($d);
            $key = $dayCarbon->toDateString();
            $t = $todosByDate->get($key);

            $linksCount = $t?->links->count() ?? 0;
            $pdfsByTask = $t ? $t->pdfs->groupBy('task')->keys()->toArray() : [];
            $filledPdfTasks = count(array_intersect($pdfTaskKeys, $pdfsByTask));
            $linkTaskDone = $linksCount >= 12 ? 1 : 0;

            // Khusus tugas 7: bernilai 1 jika ada prospek yang dikirim pada tanggal ini dan seluruhnya sudah diupdate
            $dayProspects = $userProspectsByDate->get($key, collect());
            $task7Done = ($dayProspects->isNotEmpty() && $dayProspects->every(fn ($p) => isset($updatedProspectMap[$p->id]))) ? 1 : 0;

            $tasksDone = $linkTaskDone + $filledPdfTasks + $task7Done;
            $state = $tasksDone === $totalTasks ? 'ok'
                : ($tasksDone > 0 ? 'partial' : 'empty');

            $monthStatus[$key] = [
                'links' => $linksCount,
                'pdfs' => $t?->pdfs->count() ?? 0,
                'tasksDone' => $tasksDone,
                'tasksTotal' => $totalTasks,
                'state' => $state,
            ];
        }

        // Info prospek pada tanggal yang sedang dibuka ($date)
        $currentDateStr = $date->toDateString();
        $targetProspectsForDay = $userProspectsByDate->get($currentDateStr, collect());
        $dayTargetCount = $targetProspectsForDay->count();
        $dayCompletedCount = $targetProspectsForDay->filter(fn ($p) => isset($updatedProspectMap[$p->id]))->count();
        $isDayFullyCompleted = $dayTargetCount > 0 && ($dayCompletedCount >= $dayTargetCount);

        // Clean lightweight array for high-performance compact table in Task 7
        $prospectsData = $monthProspects->map(function ($p) use ($updatedProspectMap, $currentDateStr) {
            $phone = (string) ($p->client_phone ?? '');
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            if (str_starts_with($cleanPhone, '0')) {
                $wa = '62' . substr($cleanPhone, 1);
            } else {
                $wa = $cleanPhone;
            }

            $entryDateStr = $p->entry_date ? $p->entry_date->format('Y-m-d') : '';
            $isUpdated = isset($updatedProspectMap[$p->id]);
            $updateInfo = $updatedProspectMap[$p->id] ?? null;

            return [
                'id' => $p->id,
                'phone' => $phone ?: '-',
                'wa' => $wa,
                'service' => $p->service?->name ?? 'Layanan Tidak Diketahui',
                'entry_date' => $p->entry_date ? $p->entry_date->translatedFormat('d M Y') : '-',
                'entry_date_raw' => $entryDateStr,
                'is_current_day' => $entryDateStr === $currentDateStr,
                'sender' => $p->sender?->name ?? null,
                'group' => $p->group?->name ?? null,
                'is_updated' => $isUpdated,
                'note' => $updateInfo ? (string) $updateInfo['note'] : (string) ($p->note ?? ''),
                'updated_at_human' => $updateInfo && $updateInfo['updated_at'] ? Carbon::parse($updateInfo['updated_at'])->diffForHumans() : null,
            ];
        })->values()->toArray();

        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $date->copy()->endOfWeek(Carbon::SUNDAY);

        // Catatan umum cadangan di minggu yang sama
        $weekNoteTodo = null;
        if (empty(trim($todo->prospect_progress_note ?? ''))) {
            $weekNoteTodo = Todo::where('user_id', $request->user()->id)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->whereNotNull('prospect_progress_note')
                ->where('prospect_progress_note', '!=', '')
                ->latest('date')
                ->first();
        }

        $weekNote = $weekNoteTodo?->prospect_progress_note;
        $weekNoteDate = $weekNoteTodo ? $weekNoteTodo->date->translatedFormat('l, d F Y') : null;

        $monthDays = $date->daysInMonth;
        $firstDow = $monthStart->dayOfWeek; // 0=Sun..6=Sat
        $todayKey = now()->toDateString();
        $showTodo = $request->boolean('view') || $request->has('view');

        return view('todos.daily', [
            'date' => $date,
            'todo' => $todo,
            'weekNote' => $weekNote,
            'weekNoteDate' => $weekNoteDate,
            'platforms' => self::PLATFORMS,
            'pdfTasks' => self::PDF_TASKS,
            'linksByKey' => $linksByKey,
            'monthStatus' => $monthStatus,
            'monthStart' => $monthStart,
            'monthDays' => $monthDays,
            'firstDow' => $firstDow,
            'todayKey' => $todayKey,
            'showTodo' => $showTodo,
            'lockSetting' => $lockSetting,
            'onProcessProspects' => $monthProspects,
            'prospectsData' => $prospectsData,
            'targetCount' => $dayTargetCount,
            'completedCountThisWeek' => $dayCompletedCount,
            'isWeekFullyCompleted' => $isDayFullyCompleted,
            'dayTargetCount' => $dayTargetCount,
            'dayCompletedCount' => $dayCompletedCount,
            'isDayFullyCompleted' => $isDayFullyCompleted,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'activeWeekYear' => (int) $date->format('o'),
            'activeIsoWeek' => (int) $date->format('W'),
        ]);
    }

    public function storeDaily(Request $request): JsonResponse|RedirectResponse
    {
        $this->assertMarketing($request);

        $lockSetting = TodoLockSetting::instance();
        if ($lockSetting->is_locked) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => $lockSetting->reason ?: 'To Do harian sedang direkap oleh Manager Marketing.'], 423);
            }
            return back()->with('error', $lockSetting->reason ?: 'To Do harian sedang direkap oleh Manager Marketing. Silakan tunggu sampai diaktifkan kembali.');
        }

        $data = $request->validate([
            'date' => ['required', 'date'],
            'task' => ['nullable', 'integer', 'min:1', 'max:7'],
            'prospect_progress_note' => ['nullable', 'string'],
            'prospect_updates' => ['nullable', 'array'],
            'single_prospect_id' => ['nullable', 'integer'],
            'single_note' => ['nullable', 'string'],
            'pdfs' => ['nullable', 'array'],
            'pdfs.*' => ['nullable'],
            'pdfs.*.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:102400'],
        ], [
            'pdfs.*.*.mimes' => 'Format file bukti harus berupa PDF atau Gambar (JPG, JPEG, PNG, WEBP).',
            'pdfs.*.*.max' => 'Ukuran file bukti maksimal 100MB.',
        ]);

        $date = Carbon::parse($data['date']);
        $task = isset($data['task']) ? (int) $data['task'] : 1;
        $todo = $this->getOrCreateTodo($request, $date);

        // Simpan 12 link — hanya kalau form kirim field 'links' (artinya submit Tugas 1)
        if ($request->has('links')) {
            foreach (self::PLATFORMS as $p) {
                for ($slot = 1; $slot <= 3; $slot++) {
                    $field = "links.{$p['key']}.{$slot}";
                    $url = trim((string) $request->input($field, ''));
                    if ($url === '') {
                        $todo->links()->where('platform', $p['key'])->where('slot', $slot)->delete();

                        continue;
                    }
                    TodoLink::updateOrCreate(
                        ['todo_id' => $todo->id, 'platform' => $p['key'], 'slot' => $slot],
                        ['url' => $url],
                    );
                }
            }
        }

        // Upload PDF tasks 2-6 (hanya 1 file per tugas per hari, menggantikan file lama jika ada)
        if ($request->file('pdfs')) {
            foreach ($request->file('pdfs') as $taskKey => $files) {
                $t = (int) $taskKey;
                if (! array_key_exists($t, self::PDF_TASKS)) {
                    continue;
                }

                $fileList = is_array($files) ? $files : [$files];
                $validFile = null;
                foreach ($fileList as $file) {
                    if ($file && $file->isValid()) {
                        $validFile = $file;
                        break;
                    }
                }

                if ($validFile) {
                    // Hapus semua file lama untuk tugas ini agar tergantikan (maks 1 file)
                    $oldPdfs = $todo->pdfs()->where('task', $t)->get();
                    foreach ($oldPdfs as $oldPdf) {
                        Storage::disk('public')->delete($oldPdf->file_path);
                        $oldPdf->delete();
                    }

                    $path = $validFile->store("todos/{$todo->id}", 'public');
                    TodoPdf::create([
                        'todo_id' => $todo->id,
                        'task' => $t,
                        'file_path' => $path,
                        'original_name' => $validFile->getClientOriginalName(),
                    ]);
                }
            }
        }

        // Simpan catatan perkembangan prospek (Tugas 7)
        if ($request->has('prospect_updates') || $request->filled('single_prospect_id') || $request->has('prospect_progress_note') || (int) $task === 7) {
            $wYear = (int) $date->format('o');
            $wIso = (int) $date->format('W');
            $wMonth = (int) $date->month;
            $wWeekOfMonth = min(5, max(1, (int) ceil($date->day / 7)));

            // Single update via direct submit / AJAX
            if ($request->filled('single_prospect_id')) {
                $singleId = $request->integer('single_prospect_id');
                $singleNote = trim((string) $request->input('single_note', ''));
                $p = Prospect::where('id', $singleId)->where('marketing_user_id', $request->user()->id)->first();
                if ($p) {
                    $p->update(['note' => $singleNote !== '' ? $singleNote : null]);
                    if ($singleNote !== '') {
                        ProspectWeeklyUpdate::updateOrCreate(
                            [
                                'prospect_id' => $p->id,
                                'year' => $wYear,
                                'iso_week' => $wIso,
                            ],
                            [
                                'user_id' => $request->user()->id,
                                'month' => $wMonth,
                                'week_of_month' => $wWeekOfMonth,
                                'note' => $singleNote,
                                'progress' => 'on_track',
                            ]
                        );
                    } else {
                        ProspectWeeklyUpdate::where('prospect_id', $p->id)
                            ->where('year', $wYear)
                            ->where('iso_week', $wIso)
                            ->delete();
                    }
                }
            }

            // Bulk updates (semua prospek sekaligus dari tabel)
            if ($request->has('prospect_updates') && is_array($request->input('prospect_updates'))) {
                foreach ($request->input('prospect_updates') as $pId => $uData) {
                    $p = Prospect::where('id', (int) $pId)->where('marketing_user_id', $request->user()->id)->first();
                    if (! $p) {
                        continue;
                    }
                    $pNote = trim((string) ($uData['note'] ?? ''));
                    $p->update(['note' => $pNote !== '' ? $pNote : null]);
                    if ($pNote !== '') {
                        ProspectWeeklyUpdate::updateOrCreate(
                            [
                                'prospect_id' => $p->id,
                                'year' => $wYear,
                                'iso_week' => $wIso,
                            ],
                            [
                                'user_id' => $request->user()->id,
                                'month' => $wMonth,
                                'week_of_month' => $wWeekOfMonth,
                                'note' => $pNote,
                                'progress' => $uData['progress'] ?? 'on_track',
                            ]
                        );
                    } else {
                        ProspectWeeklyUpdate::where('prospect_id', $p->id)
                            ->where('year', $wYear)
                            ->where('iso_week', $wIso)
                            ->delete();
                    }
                }
            }

            // Simpan catatan umum jika dikirim
            if ($request->has('prospect_progress_note')) {
                $note = trim((string) $request->input('prospect_progress_note', ''));
                $todo->update(['prospect_progress_note' => $note !== '' ? $note : null]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                $dayProspects = Prospect::where('marketing_user_id', $request->user()->id)
                    ->whereDate('entry_date', $date->toDateString())
                    ->get();
                $dayTargetCount = $dayProspects->count();
                $dayCompletedCount = 0;

                if ($dayTargetCount > 0) {
                    $dayUpdates = ProspectWeeklyUpdate::whereIn('prospect_id', $dayProspects->pluck('id'))
                        ->whereNotNull('note')
                        ->where('note', '!=', '')
                        ->get()
                        ->groupBy('prospect_id');

                    foreach ($dayProspects as $dp) {
                        if (! empty(trim((string) $dp->note)) || $dayUpdates->has($dp->id)) {
                            $dayCompletedCount++;
                        }
                    }
                }

                $isDayComplete = $dayTargetCount > 0 && ($dayCompletedCount >= $dayTargetCount);

                return response()->json([
                    'success' => true,
                    'message' => 'Catatan perkembangan prospek berhasil disimpan.',
                    'completed_count' => $dayCompletedCount,
                    'target_count' => $dayTargetCount,
                    'is_day_completed' => $isDayComplete,
                    'is_week_completed' => $isDayComplete,
                ]);
            }
        }

        return redirect()->route('todos.daily', [
            'date' => $date->toDateString(),
            'view' => 1,
            'tab' => $task,
        ])->with('status', 'Tugas '.$task.' berhasil disimpan.');
    }

    public function destroyPdf(Request $request, TodoPdf $pdf): RedirectResponse
    {
        $this->assertMarketing($request);
        abort_unless($pdf->todo->user_id === $request->user()->id, 403);

        $lockSetting = TodoLockSetting::instance();
        if ($lockSetting->is_locked) {
            return back()->with('error', 'To Do harian sedang direkap oleh Manager Marketing.');
        }

        Storage::disk('public')->delete($pdf->file_path);
        $pdf->delete();

        return back()->with('status', 'File dihapus.');
    }

    private function getOrCreateTodo(Request $request, Carbon $date): Todo
    {
        return Todo::firstOrCreate([
            'user_id' => $request->user()->id,
            'date' => $date->toDateString(),
        ]);
    }

    private function assertMarketing(Request $request): void
    {
        abort_unless($request->user()->role?->slug === 'marketing', 403);
    }
}
