<?php

namespace App\Http\Controllers;

use App\Jobs\ExportTodosJob;
use App\Models\Prospect;
use App\Models\ProspectWeeklyUpdate;
use App\Models\Todo;
use App\Models\TodoExport;
use App\Models\TodoLink;
use App\Models\TodoLockSetting;
use App\Models\TodoPdf;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class ManagerController extends Controller
{
    public function todos(Request $request): View
    {
        $this->assertManager($request);

        $month = $request->integer('month', (int) now()->month);
        $year = $request->integer('year', (int) now()->year);

        $currentDate = Carbon::createFromDate($year, $month, 1);
        $monthStart = $currentDate->copy()->startOfMonth();
        $monthEnd = $currentDate->copy()->endOfMonth();
        $daysInMonth = $currentDate->daysInMonth;

        $lockSetting = TodoLockSetting::instance();
        $marketings = $this->marketings();

        // Ambil semua todo dari awal minggu (Senin) di awal bulan sampai akhir minggu (Minggu) di akhir bulan
        // agar Todo 7 mingguan (Senin-Minggu) terhitung akurat
        $queryStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $queryEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        $allTodos = Todo::with(['links', 'pdfs', 'user'])
            ->whereBetween('date', [$queryStart, $queryEnd])
            ->whereHas('user.role', fn ($q) => $q->where('slug', 'marketing'))
            ->get();

        // Standard 7 Tugas Harian
        $tasksList = [
            1 => ['title' => 'Posting 12 Link Media Sosial', 'desc' => 'Mengisi 12 link postingan (Instagram, TikTok, FB, Other Video)', 'type' => 'link'],
            2 => ['title' => 'Broadcast & Komentar Sosial Media', 'desc' => 'Upload bukti PDF broadcast & komentar', 'type' => 'pdf'],
            3 => ['title' => 'Mengiklankan Akun Instagram', 'desc' => 'Upload bukti PDF iklan Instagram', 'type' => 'pdf'],
            4 => ['title' => 'DM Brosur', 'desc' => 'Upload bukti PDF DM brosur (Pak Sabar, Pak Henry, Marketing)', 'type' => 'pdf'],
            5 => ['title' => 'Menyapa & Follow Up Klien Lama', 'desc' => 'Upload bukti PDF sapa/follow up klien lama', 'type' => 'pdf'],
            6 => ['title' => 'Memaparkan Rencana Penjualan', 'desc' => 'Upload bukti PDF rencana penjualan', 'type' => 'pdf'],
            7 => ['title' => 'Update Perkembangan Prospek', 'desc' => 'Update perkembangan prospek yang dikirim pada tanggal tersebut', 'type' => 'note'],
        ];

        // Ambil prospek pada bulan ini untuk semua marketing
        $targetProspects = Prospect::whereMonth('entry_date', $month)
            ->whereYear('entry_date', $year)
            ->get(['id', 'marketing_user_id', 'entry_date', 'note']);

        $targetProspectsByUser = $targetProspects->groupBy('marketing_user_id');
        $allTargetProspectIds = $targetProspects->pluck('id');

        $allWeeklyUpdates = ProspectWeeklyUpdate::whereIn('prospect_id', $allTargetProspectIds)
            ->whereNotNull('note')
            ->where('note', '!=', '')
            ->get(['id', 'prospect_id', 'note', 'year', 'iso_week']);

        // Map id prospek yang sudah terupdate (baik di tabel prospects.note atau prospect_weekly_updates)
        $updatedProspectMap = [];
        foreach ($targetProspects as $tp) {
            if (! empty(trim((string) $tp->note))) {
                $updatedProspectMap[$tp->id] = true;
            }
        }
        foreach ($allWeeklyUpdates as $wu) {
            if (! empty(trim((string) $wu->note))) {
                $updatedProspectMap[$wu->prospect_id] = true;
            }
        }

        $matrixData = $marketings->map(function ($m) use ($allTodos, $daysInMonth, $monthStart, $targetProspectsByUser, $updatedProspectMap) {
            $userTodos = $allTodos->where('user_id', $m->id);
            $userTodosByDate = $userTodos->keyBy(fn ($t) => $t->date->format('Y-m-d'));

            // Prospek marketing ini di kelompokkan berdasarkan tanggal kirim (entry_date)
            $userProspects = $targetProspectsByUser->get($m->id, collect());
            $userProspectsByDate = $userProspects->groupBy(fn ($p) => $p->entry_date->format('Y-m-d'));

            // Reorganisasi status 7 tugas per tanggal untuk marketing ini
            $dailyTasks = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dayDate = $monthStart->copy()->day($d);
                $dateStr = $dayDate->format('Y-m-d');
                $todo = $userTodosByDate->get($dateStr);

                $linksCount = $todo?->links->count() ?? 0;
                $pdfsByTask = $todo ? $todo->pdfs->groupBy('task') : collect();

                // Tugas 7: bernilai 'X' jika ada prospek yang dikirim pada tanggal ini dan seluruhnya sudah diupdate
                $dayProspects = $userProspectsByDate->get($dateStr, collect());
                $hasTask7 = $dayProspects->isNotEmpty() && $dayProspects->every(fn ($p) => isset($updatedProspectMap[$p->id]));

                $dailyTasks[$d] = [
                    1 => $linksCount >= 12,
                    2 => $pdfsByTask->has(2) && $pdfsByTask->get(2)->count() > 0,
                    3 => $pdfsByTask->has(3) && $pdfsByTask->get(3)->count() > 0,
                    4 => $pdfsByTask->has(4) && $pdfsByTask->get(4)->count() > 0,
                    5 => $pdfsByTask->has(5) && $pdfsByTask->get(5)->count() > 0,
                    6 => $pdfsByTask->has(6) && $pdfsByTask->get(6)->count() > 0,
                    7 => $hasTask7,
                ];
            }

            // Hitung total keaktifan mengisi
            $totalFilledDays = count(array_filter($dailyTasks, function ($day) {
                return in_array(true, $day, true);
            }));

            return [
                'user' => $m,
                'dailyTasks' => $dailyTasks,
                'totalFilledDays' => $totalFilledDays,
                'todosByDate' => $userTodosByDate,
            ];
        });

        return view('manager.todos', [
            'month' => $month,
            'year' => $year,
            'monthLabel' => $currentDate->translatedFormat('F Y'),
            'daysInMonth' => $daysInMonth,
            'tasksList' => $tasksList,
            'matrixData' => $matrixData,
            'lockSetting' => $lockSetting,
        ]);
    }

    public function exportTodos(Request $request): Response
    {
        $this->assertManager($request);

        Carbon::setLocale('id');

        $type = $request->string('type', 'monthly')->value();

        if ($type === 'range') {
            $startDate = Carbon::parse($request->string('start_date', now()->startOfWeek(Carbon::MONDAY)->toDateString()))->startOfDay();
            $endDate = Carbon::parse($request->string('end_date', now()->endOfWeek(Carbon::SUNDAY)->toDateString()))->startOfDay();
            if ($startDate->gt($endDate)) {
                $temp = $startDate->copy();
                $startDate = $endDate->copy();
                $endDate = $temp;
            }
            $periodLabel = $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y');
            $filePeriodTag = $startDate->format('Ymd') . '_sd_' . $endDate->format('Ymd');
        } elseif ($type === 'daily') {
            $startDate = Carbon::parse($request->string('single_date', $request->string('date', now()->toDateString())))->startOfDay();
            $endDate = $startDate->copy();
            $periodLabel = $startDate->translatedFormat('d F Y');
            $filePeriodTag = 'Harian_' . $startDate->format('Ymd');
        } else {
            $type = 'monthly';
            $month = $request->integer('month', (int) now()->month);
            $year = $request->integer('year', (int) now()->year);
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $periodLabel = $startDate->translatedFormat('F Y');
            $filePeriodTag = str_replace(' ', '_', $periodLabel);
        }

        $tasksList = [
            1 => ['title' => 'Posting 12 Link Media Sosial', 'desc' => '12 link (Instagram, TikTok, FB, Other Video)'],
            2 => ['title' => 'Broadcast & Komentar Sosial Media', 'desc' => 'Bukti PDF broadcast & komentar'],
            3 => ['title' => 'Mengiklankan Akun Instagram', 'desc' => 'Bukti PDF iklan Instagram'],
            4 => ['title' => 'DM Brosur', 'desc' => 'Bukti PDF DM brosur (Pak Sabar, Pak Henry, Marketing)'],
            5 => ['title' => 'Menyapa & Follow Up Klien Lama', 'desc' => 'Bukti PDF sapa/follow up klien lama'],
            6 => ['title' => 'Memaparkan Rencana Penjualan', 'desc' => 'Bukti PDF rencana penjualan'],
            7 => ['title' => 'Update Perkembangan Prospek', 'desc' => 'Catatan mingguan (Senin - Minggu)'],
        ];

        // Jika request user_id tertentu (download langsung single excel marketing)
        if ($request->filled('user_id')) {
            $user = User::whereHas('role', fn ($q) => $q->where('slug', 'marketing'))->findOrFail($request->integer('user_id'));

            $userTodos = Todo::with(['links', 'pdfs'])
                ->where('user_id', $user->id)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get();

            $targetProspects = Prospect::with(['service', 'sender'])
                ->where('marketing_user_id', $user->id)
                ->whereBetween('entry_date', [
                    $startDate->copy()->startOfDay(),
                    $endDate->copy()->endOfDay()
                ])
                ->get();

            $targetProspectsByUser = $targetProspects->groupBy('marketing_user_id');
            $allWeeklyUpdates = ProspectWeeklyUpdate::with(['prospect.service', 'prospect.sender'])
                ->whereIn('prospect_id', $targetProspects->pluck('id'))
                ->whereNotNull('note')
                ->where('note', '!=', '')
                ->get();

            $spreadsheet = $this->buildMarketingSpreadsheet(
                $user,
                $startDate,
                $endDate,
                $periodLabel,
                $userTodos,
                $targetProspectsByUser,
                $allWeeklyUpdates,
                $tasksList,
                $type
            );

            $cleanName = preg_replace('/[\\\\\/:\*\?"<>\|]/', '', trim((string) $user->name));
            $cleanName = trim($cleanName) ?: 'Marketing';
            $filename = 'Rekap_Todos_' . str_replace(' ', '_', $cleanName) . '_' . $filePeriodTag . '.xlsx';

            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                $spreadsheet->disconnectWorksheets();
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
                'Pragma' => 'public',
            ]);
        }

        // Default: Export seluruh marketing dalam file ZIP berisi masing-masing 1 file Excel per marketing
        $marketings = $this->marketings();

        if ($marketings->isEmpty()) {
            return back()->with('status', 'Tidak ada karyawan dengan role Marketing yang ditemukan.');
        }

        $allTodos = Todo::with(['links', 'pdfs'])
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('user.role', fn ($q) => $q->where('slug', 'marketing'))
            ->get();

        $targetProspects = Prospect::with(['service', 'sender'])
            ->whereBetween('entry_date', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay()
            ])
            ->get();

        $targetProspectsByUser = $targetProspects->groupBy('marketing_user_id');
        $allTargetProspectIds = $targetProspects->pluck('id');

        $allWeeklyUpdates = ProspectWeeklyUpdate::with(['prospect.service', 'prospect.sender'])
            ->whereIn('prospect_id', $allTargetProspectIds)
            ->whereNotNull('note')
            ->where('note', '!=', '')
            ->get();

        // Buat temporary ZIP file
        $tempZipPath = tempnam(sys_get_temp_dir(), 'todos_zip_');
        if (file_exists($tempZipPath)) {
            @unlink($tempZipPath);
        }
        $tempZipPath .= '.zip';

        $zip = new ZipArchive();
        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file arsip ZIP untuk export to-do.');
        }

        $createdTempFiles = [];
        $usedFileNames = [];

        foreach ($marketings as $idx => $m) {
            $userTodos = $allTodos->where('user_id', $m->id);

            $spreadsheet = $this->buildMarketingSpreadsheet(
                $m,
                $startDate,
                $endDate,
                $periodLabel,
                $userTodos,
                $targetProspectsByUser,
                $allWeeklyUpdates,
                $tasksList,
                $type
            );

            // Simpan spreadsheet ke temporary file
            $tempXlsx = tempnam(sys_get_temp_dir(), 'mkt_xlsx_');
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempXlsx);
            $createdTempFiles[] = $tempXlsx;

            // Free memory segera
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            // Bersihkan nama file di dalam ZIP
            $cleanName = preg_replace('/[\\\\\/:\*\?"<>\|]/', '', trim((string) $m->name));
            $cleanName = trim($cleanName);
            if ($cleanName === '') {
                $cleanName = 'Marketing_' . ($idx + 1);
            }
            $cleanName = str_replace(' ', '_', $cleanName);

            $baseFileName = 'Rekap_Todos_' . $cleanName . '_' . $filePeriodTag;
            $excelFileName = $baseFileName . '.xlsx';
            $counter = 1;
            while (in_array(strtolower($excelFileName), $usedFileNames, true)) {
                $excelFileName = $baseFileName . '_' . $counter . '.xlsx';
                $counter++;
            }
            $usedFileNames[] = strtolower($excelFileName);

            $zip->addFile($tempXlsx, $excelFileName);
        }

        $zip->close();

        // Hapus temporary xlsx files yang sudah masuk ke zip
        foreach ($createdTempFiles as $tempFile) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        $zipDownloadName = 'Rekap_Todos_Marketing_' . $filePeriodTag . '.zip';

        return response()->download($tempZipPath, $zipDownloadName, [
            'Content-Type' => 'application/zip',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'public',
        ])->deleteFileAfterSend(true);
    }

    public function startExport(Request $request): JsonResponse
    {
        $this->assertManager($request);

        $type = $request->string('type', 'monthly')->value();

        if ($type === 'range') {
            if (!$request->filled('start_date') || !$request->filled('end_date')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tanggal awal dan tanggal akhir wajib diisi untuk export mingguan / rentang tanggal.',
                ], 422);
            }
            $startDate = Carbon::parse($request->string('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->string('end_date'))->startOfDay();
            if ($startDate->gt($endDate)) {
                $temp = $startDate->copy();
                $startDate = $endDate->copy();
                $endDate = $temp;
            }
            $periodLabel = $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y');
        } elseif ($type === 'daily') {
            if (!$request->filled('single_date') && !$request->filled('date')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tanggal wajib dipilih untuk export harian.',
                ], 422);
            }
            $startDate = Carbon::parse($request->string('single_date', $request->string('date')))->startOfDay();
            $endDate = $startDate->copy();
            $periodLabel = $startDate->translatedFormat('d F Y');
        } else {
            $type = 'monthly';
            $month = $request->integer('month', (int) now()->month);
            $year = $request->integer('year', (int) now()->year);
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $periodLabel = $startDate->translatedFormat('F Y');
        }

        $marketingsCount = User::whereHas('role', fn ($q) => $q->where('slug', 'marketing'))->count();
        if ($marketingsCount === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada karyawan dengan role Marketing yang ditemukan.',
            ], 422);
        }

        // Cek jika sudah ada export yang sedang berjalan
        $existingQuery = TodoExport::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'processing']);

        if ($type === 'monthly') {
            $existingQuery->where('month', $startDate->month)->where('year', $startDate->year);
        } elseif ($type === 'daily') {
            $existingQuery->whereDate('start_date', $startDate->toDateString());
        } else {
            $existingQuery->whereDate('start_date', $startDate->toDateString())->whereDate('end_date', $endDate->toDateString());
        }

        $existing = $existingQuery->latest()->first();

        if ($existing) {
            self::ensureQueueWorkerRunning();
            return response()->json([
                'status' => 'success',
                'export_id' => $existing->id,
                'percent' => $existing->percent,
                'processed' => $existing->processed_marketing,
                'total' => $existing->total_marketing,
                'export_status' => $existing->status,
                'message' => 'Proses export sedang berlangsung di background...',
            ]);
        }

        $export = TodoExport::create([
            'user_id' => $request->user()->id,
            'month' => $startDate->month,
            'year' => $startDate->year,
            'export_type' => $type,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'period_label' => $periodLabel,
            'status' => 'pending',
            'total_marketing' => $marketingsCount,
            'processed_marketing' => 0,
        ]);

        ExportTodosJob::dispatch(
            $export,
            $type,
            $startDate->toDateString(),
            $endDate->toDateString(),
            $periodLabel
        );
        self::ensureQueueWorkerRunning();

        $export->refresh();

        return response()->json([
            'status' => 'success',
            'export_id' => $export->id,
            'percent' => $export->percent,
            'processed' => $export->processed_marketing,
            'total' => $marketingsCount,
            'export_status' => $export->status,
            'message' => $export->status === 'completed' ? 'Export berhasil selesai dibuat.' : 'Export berhasil dijadwalkan di background.',
        ]);
    }

    public function exportStatus(Request $request, TodoExport $export): JsonResponse
    {
        $this->assertManager($request);
        abort_unless($export->user_id === $request->user()->id || $request->user()->role?->slug === 'super_admin', 403);

        if (in_array($export->status, ['pending', 'processing'], true)) {
            // Deteksi jika antrean macet di server (worker tidak jalan > 3 menit dan progress 0)
            if ($export->created_at && $export->created_at->diffInMinutes(now()) >= 3 && $export->processed_marketing === 0) {
                $export->update([
                    'status' => 'failed',
                    'error_message' => 'Antrean export tidak berjalan di server (Queue worker tidak aktif). Silakan jalankan queue worker atau ubah QUEUE_CONNECTION=sync di .env.',
                ]);
            }
        }

        return response()->json([
            'id' => $export->id,
            'status' => $export->status,
            'percent' => $export->percent,
            'processed' => $export->processed_marketing,
            'total' => $export->total_marketing,
            'filename' => $export->filename,
            'download_url' => route('manager.todos.export.download', $export),
            'error_message' => $export->error_message,
            'completed_at' => $export->completed_at?->translatedFormat('d M Y H:i'),
        ]);
    }

    public function downloadExport(Request $request, TodoExport $export)
    {
        $this->assertManager($request);
        abort_unless($export->user_id === $request->user()->id || $request->user()->role?->slug === 'super_admin', 403);

        abort_unless($export->status === 'completed' && $export->file_path && file_exists($export->file_path), 404, 'File export belum tersedia atau sudah kedaluwarsa.');

        return response()->download($export->file_path, $export->filename, [
            'Content-Type' => 'application/zip',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    public function recentExports(Request $request): JsonResponse
    {
        $this->assertManager($request);

        $exports = TodoExport::where('user_id', $request->user()->id)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($e) {
                $label = $e->period_label;
                if (empty($label)) {
                    $monthCarbon = Carbon::createFromDate($e->year, $e->month, 1);
                    $label = $monthCarbon->translatedFormat('F Y');
                }
                return [
                    'id' => $e->id,
                    'month_label' => $label,
                    'status' => $e->status,
                    'percent' => $e->percent,
                    'processed' => $e->processed_marketing,
                    'total' => $e->total_marketing,
                    'download_url' => route('manager.todos.export.download', $e),
                    'time_ago' => $e->created_at->diffForHumans(),
                    'filename' => $e->filename,
                ];
            });

        return response()->json([
            'exports' => $exports,
            'has_active' => $exports->contains(fn ($e) => in_array($e['status'], ['pending', 'processing'], true)),
        ]);
    }

    public static function ensureQueueWorkerRunning(): void
    {
        if (config('queue.default') === 'sync') {
            return;
        }

        // Jangan spawn worker berkali-kali jika sudah dipicu dalam 120 detik terakhir
        if (! Cache::add('export_worker_spawn_lock', true, 120)) {
            return;
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $artisanPath = base_path('artisan');

        try {
            if ($isWindows) {
                $phpBinary = PHP_BINARY;
                $cmd = "start /B \"\" \"{$phpBinary}\" \"{$artisanPath}\" queue:work --stop-when-empty --timeout=900 --memory=512 > NUL 2>&1";
                @pclose(@popen($cmd, 'r'));
            } else {
                // Di Linux, jika PHP berjalan di bawah FPM (Nginx/Apache), PHP_BINARY merujuk ke php-fpm bukan php-cli
                $phpBinary = PHP_BINARY;
                if (str_contains($phpBinary, 'fpm') || ! is_executable($phpBinary)) {
                    if (file_exists('/usr/bin/php') && is_executable('/usr/bin/php')) {
                        $phpBinary = '/usr/bin/php';
                    } elseif (file_exists('/usr/local/bin/php') && is_executable('/usr/local/bin/php')) {
                        $phpBinary = '/usr/local/bin/php';
                    } else {
                        $phpBinary = 'php';
                    }
                }

                $cmd = "\"{$phpBinary}\" \"{$artisanPath}\" queue:work --stop-when-empty --timeout=900 --memory=512 > /dev/null 2>&1 &";
                @exec($cmd);
            }
        } catch (\Throwable $e) {
            // Ignored; worker handled by daemon if any
        }
    }

    private function buildMarketingSpreadsheet(
        User $m,
        Carbon $startDate,
        Carbon|int $endDateOrDays,
        string $periodLabel,
        $userTodos,
        $targetProspectsByUser,
        $allWeeklyUpdates,
        array $tasksList,
        string $exportType = 'monthly'
    ): Spreadsheet {
        $job = new ExportTodosJob(new TodoExport());
        return $job->buildFastMarketingSpreadsheet(
            $m,
            $startDate,
            $endDateOrDays,
            $periodLabel,
            $userTodos,
            $targetProspectsByUser,
            $allWeeklyUpdates,
            $tasksList,
            $exportType
        );
    }

    public function toggleTodoLock(Request $request): RedirectResponse
    {
        $this->assertManager($request);

        $setting = TodoLockSetting::instance();
        if ($setting->is_locked) {
            $setting->unlock($request->user()->id);
            $msg = 'To Do harian tim marketing berhasil dibuka. Tim marketing dapat mengisi kembali.';
        } else {
            $reason = trim((string) $request->input('reason', ''));
            if ($reason === '') {
                $reason = 'To Do harian sedang direkap oleh Manager Marketing. Silakan tunggu sampai diaktifkan kembali.';
            }
            $setting->lock($request->user()->id, $reason);
            $msg = 'To Do harian tim marketing berhasil dikunci untuk rekap.';
        }

        return back()->with('status', $msg);
    }

    public function todoData(Request $request, User $user)
    {
        $this->assertManager($request);
        abort_unless($user->role?->slug === 'marketing', 404);

        $dateStr = $request->string('date', now()->toDateString());
        $date = Carbon::parse($dateStr);

        $todo = Todo::with(['links', 'pdfs'])->firstOrCreate([
            'user_id' => $user->id,
            'date' => $date->toDateString(),
        ]);

        $linksByKey = $todo->links()->get()->groupBy('platform')->map->keyBy('slot');

        $prospectNote = $todo->prospect_progress_note ?? '';
        $noteDateInfo = null;

        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $date->copy()->endOfWeek(Carbon::SUNDAY);
        $wYear = (int) $date->format('o');
        $wIso = (int) $date->format('W');

        // Prospek yang dikirim CS ke marketing ini pada tanggal terkait ($date)
        $dayProspects = Prospect::with(['service', 'sender', 'group', 'status'])
            ->where('marketing_user_id', $user->id)
            ->whereDate('entry_date', $date->toDateString())
            ->orderBy('id', 'asc')
            ->get();

        $targetCount = $dayProspects->count();
        $dayProspectIds = $dayProspects->pluck('id');

        $weeklyUpdates = ProspectWeeklyUpdate::whereIn('prospect_id', $dayProspectIds)
            ->get()
            ->groupBy('prospect_id');

        $prospectsList = $dayProspects->map(function ($p) use ($weeklyUpdates) {
            $latestUpdate = $weeklyUpdates->get($p->id)?->sortByDesc('updated_at')->first();
            $note = trim((string) ($latestUpdate?->note ?? $p->note ?? ''));
            return [
                'id' => $p->id,
                'phone' => $p->client_phone,
                'service' => $p->service?->name ?? 'Layanan Tidak Diketahui',
                'entry_date' => $p->entry_date ? $p->entry_date->translatedFormat('d M Y') : '-',
                'sender' => $p->sender?->name ?? '-',
                'has_update' => ! empty($note),
                'note' => $note,
                'updated_at' => $latestUpdate?->updated_at ? $latestUpdate->updated_at->diffForHumans() : ($p->updated_at ? $p->updated_at->diffForHumans() : null),
            ];
        });

        $completedProspectsCount = $prospectsList->where('has_update', true)->count();
        $isDayComplete = $targetCount > 0 ? ($completedProspectsCount >= $targetCount) : false;

        $taskUpdates = $this->buildTaskUpdates($todo);
        $formattedPdfs = $this->formatPdfsGrouped($todo);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
            ],
            'date' => $date->toDateString(),
            'formattedDate' => $date->translatedFormat('l, d F Y'),
            'todo_id' => $todo->id,
            'prospect_progress_note' => $prospectNote,
            'note_date_info' => $noteDateInfo,
            'links' => $linksByKey,
            'pdfs' => $formattedPdfs,
            'task_updates' => $taskUpdates,
            'prospects_list' => $prospectsList,
            'target_count' => $targetCount,
            'completed_prospects_count' => $completedProspectsCount,
            'is_week_complete' => $isDayComplete,
            'is_day_complete' => $isDayComplete,
            'week_range' => $date->translatedFormat('d F Y'),
        ]);
    }

    public function updateTodo(Request $request, User $user)
    {
        $this->assertManager($request);
        abort_unless($user->role?->slug === 'marketing', 404);

        $data = $request->validate([
            'date' => ['required', 'date'],
            'task' => ['nullable', 'integer', 'min:1', 'max:7'],
            'prospect_progress_note' => ['nullable', 'string'],
            'prospect_updates' => ['nullable', 'array'],
            'pdfs' => ['nullable', 'array'],
            'pdfs.*' => ['nullable'],
            'pdfs.*.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'pdfs.*.*.mimes' => 'Format file bukti harus berupa PDF atau Gambar (JPG, JPEG, PNG, WEBP).',
            'pdfs.*.*.max' => 'Ukuran file bukti maksimal 10MB.',
        ]);

        $date = Carbon::parse($data['date']);
        $task = isset($data['task']) ? (int) $data['task'] : 1;

        $todo = Todo::firstOrCreate([
            'user_id' => $user->id,
            'date' => $date->toDateString(),
        ]);

        $platforms = [
            ['key' => 'instagram'],
            ['key' => 'tiktok'],
            ['key' => 'facebook'],
            ['key' => 'snack_video'],
        ];

        // Simpan 12 link (Hanya jika sedang edit Tugas 1)
        if ($task === 1 && $request->has('links')) {
            foreach ($platforms as $p) {
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

        // Upload PDF tasks 2-6 (Hanya jika sedang edit Tugas 2-6, menggantikan file lama jika ada)
        if ($task >= 2 && $task <= 6 && $request->file('pdfs')) {
            foreach ($request->file('pdfs') as $taskKey => $files) {
                $t = (int) $taskKey;
                if ($t !== $task) {
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
                    // Hapus file lama tugas ini agar tergantikan (maks 1 file)
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

        // Simpan catatan perkembangan prospek (Hanya jika sedang edit Tugas 7)
        if ($task === 7) {
            $wYear = (int) $date->format('o');
            $wIso = (int) $date->format('W');
            $wMonth = (int) $date->month;
            $wWeekOfMonth = min(5, max(1, (int) ceil($date->day / 7)));

            if ($request->has('prospect_updates') && is_array($request->input('prospect_updates'))) {
                foreach ($request->input('prospect_updates') as $pId => $uData) {
                    $p = Prospect::where('id', (int) $pId)->where('marketing_user_id', $user->id)->first();
                    if (! $p) {
                        continue;
                    }
                    $pNote = trim((string) ($uData['note'] ?? ''));
                    if ($pNote !== '') {
                        ProspectWeeklyUpdate::updateOrCreate(
                            ['prospect_id' => $p->id, 'year' => $wYear, 'iso_week' => $wIso],
                            ['user_id' => $user->id, 'month' => $wMonth, 'week_of_month' => $wWeekOfMonth, 'note' => $pNote, 'progress' => 'on_track']
                        );
                    } else {
                        ProspectWeeklyUpdate::where('prospect_id', $p->id)->where('year', $wYear)->where('iso_week', $wIso)->delete();
                    }
                }
            }

            if ($request->has('prospect_progress_note')) {
                $note = trim((string) $request->input('prospect_progress_note', ''));
                $todo->update(['prospect_progress_note' => $note !== '' ? $note : null]);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            $todo->load(['links', 'pdfs']);

            $isCompleted = false;
            $affectedDays = [(int) $date->day];

            if ($task === 1) {
                $isCompleted = $todo->links->count() >= 12;
            } elseif ($task >= 2 && $task <= 6) {
                $isCompleted = $todo->pdfs->where('task', $task)->count() > 0;
            } elseif ($task === 7) {
                $wYear = (int) $date->format('o');
                $wIso = (int) $date->format('W');
                // Cek apakah seluruh prospek yang dikirim pada tanggal $date sudah diupdate
                $dayProspects = Prospect::where('marketing_user_id', $user->id)
                    ->whereDate('entry_date', $date->toDateString())
                    ->get();
                $targetCount = $dayProspects->count();

                if ($targetCount > 0) {
                    $dayProspectIds = $dayProspects->pluck('id');
                    $weeklyUpdates = ProspectWeeklyUpdate::whereIn('prospect_id', $dayProspectIds)
                        ->whereNotNull('note')
                        ->where('note', '!=', '')
                        ->get()
                        ->groupBy('prospect_id');

                    $updatedCount = 0;
                    foreach ($dayProspects as $dp) {
                        $hasNote = ! empty(trim((string) $dp->note)) || $weeklyUpdates->has($dp->id);
                        if ($hasNote) {
                            $updatedCount++;
                        }
                    }
                    $isCompleted = ($updatedCount >= $targetCount);
                } else {
                    $isCompleted = false;
                }

                $affectedDays = [(int) $date->day];
            }

            $taskUpdates = $this->buildTaskUpdates($todo);
            $formattedPdfs = $this->formatPdfsGrouped($todo);

            return response()->json([
                'success' => true,
                'message' => "Tugas {$task} berhasil disimpan.",
                'task' => $task,
                'day' => (int) $date->day,
                'affectedDays' => $affectedDays,
                'date' => $date->toDateString(),
                'isCompleted' => $isCompleted,
                'links' => $todo->links()->get()->groupBy('platform')->map->keyBy('slot'),
                'pdfs' => $formattedPdfs,
                'task_updates' => $taskUpdates,
                'prospect_progress_note' => $todo->prospect_progress_note,
            ]);
        }

        return back()->with('status', "To Do harian {$user->name} tanggal {$date->translatedFormat('d F Y')} berhasil diperbarui oleh Manager.");
    }

    public function destroyPdf(Request $request, TodoPdf $pdf)
    {
        $this->assertManager($request);

        $task = $pdf->task;
        $todo = $pdf->todo;
        $day = $todo ? Carbon::parse($todo->date)->day : null;
        $userId = $todo ? $todo->user_id : null;

        Storage::disk('public')->delete($pdf->file_path);
        $pdf->delete();

        if ($request->ajax() || $request->wantsJson()) {
            $remainingCount = $todo ? $todo->pdfs()->where('task', $task)->count() : 0;
            $todo?->load(['links', 'pdfs']);
            $taskUpdates = $todo ? $this->buildTaskUpdates($todo) : [];

            return response()->json([
                'success' => true,
                'message' => 'File PDF berhasil dihapus.',
                'task' => $task,
                'day' => $day,
                'userId' => $userId,
                'isCompleted' => $remainingCount > 0,
                'remainingCount' => $remainingCount,
                'task_updates' => $taskUpdates,
            ]);
        }

        return back()->with('status', 'File PDF berhasil dihapus.');
    }

    private function buildTaskUpdates(Todo $todo): array
    {
        Carbon::setLocale('id');
        $taskUpdates = [];

        // Tugas 1: 12 Link
        $filledLinks = $todo->links->filter(fn ($l) => ! empty(trim((string) $l->url)));
        if ($filledLinks->isNotEmpty()) {
            $latestLink = $filledLinks->max('updated_at') ?? $filledLinks->max('created_at');
            $c = $latestLink ? Carbon::parse($latestLink) : null;
            $taskUpdates[1] = [
                'has_update' => true,
                'updated_at' => $c ? $c->locale('id')->diffForHumans() : null,
                'updated_at_full' => $c ? $c->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : null,
                'is_recent' => $c ? ($c->diffInHours(now()) <= 24) : false,
            ];
        } else {
            $taskUpdates[1] = [
                'has_update' => false,
                'updated_at' => null,
                'updated_at_full' => null,
                'is_recent' => false,
            ];
        }

        // Tugas 2 - 6: Upload PDF / Gambar
        for ($t = 2; $t <= 6; $t++) {
            $taskPdfs = $todo->pdfs->where('task', $t);
            if ($taskPdfs->isNotEmpty()) {
                $latestPdf = $taskPdfs->max('updated_at') ?? $taskPdfs->max('created_at');
                $c = $latestPdf ? Carbon::parse($latestPdf) : null;
                $taskUpdates[$t] = [
                    'has_update' => true,
                    'updated_at' => $c ? $c->locale('id')->diffForHumans() : null,
                    'updated_at_full' => $c ? $c->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : null,
                    'is_recent' => $c ? ($c->diffInHours(now()) <= 24) : false,
                ];
            } else {
                $taskUpdates[$t] = [
                    'has_update' => false,
                    'updated_at' => null,
                    'updated_at_full' => null,
                    'is_recent' => false,
                ];
            }
        }

        return $taskUpdates;
    }

    private function formatPdfsGrouped(Todo $todo)
    {
        Carbon::setLocale('id');

        return $todo->pdfs->map(function ($pdf) {
            $c = $pdf->updated_at ?? $pdf->created_at;
            $cParsed = $c ? Carbon::parse($c) : null;

            return [
                'id' => $pdf->id,
                'todo_id' => $pdf->todo_id,
                'task' => $pdf->task,
                'file_path' => $pdf->file_path,
                'original_name' => $pdf->original_name,
                'created_at' => $pdf->created_at,
                'updated_at' => $pdf->updated_at,
                'updated_ago' => $cParsed ? $cParsed->locale('id')->diffForHumans() : null,
                'updated_full' => $cParsed ? $cParsed->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : null,
            ];
        })->groupBy('task');
    }

    private function marketings()
    {
        return User::whereHas('role', fn ($q) => $q->where('slug', 'marketing'))->orderBy('name')->get();
    }

    private function assertManager(Request $request): void
    {
        abort_unless(in_array($request->user()->role?->slug, ['manager_marketing', 'super_admin'], true), 403);
    }
}
