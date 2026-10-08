<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\ProspectLockSetting;
use App\Models\ProspectWeeklyUpdate;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $role = $user->role?->slug;

        $data = [
            'role' => $role,
            'user' => $user,
        ];

        if ($role === 'marketing') {
            $today = today();
            $data['myProspek'] = Prospect::with('status')
                ->where('marketing_user_id', $user->id)
                ->latest('entry_date')
                ->limit(5)
                ->get();
            $data['todayTodo'] = Todo::with(['links', 'pdfs'])
                ->where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();
            $data['prospekToday'] = Prospect::where('marketing_user_id', $user->id)
                ->whereDate('entry_date', $today)
                ->count();
            $data['prospekMonth'] = Prospect::where('marketing_user_id', $user->id)
                ->whereMonth('entry_date', now()->month)
                ->whereYear('entry_date', now()->year)
                ->count();

            // Status counts (total) — grouped by status slug
            $statusCounts = Prospect::where('marketing_user_id', $user->id)
                ->whereHas('status')
                ->with('status')
                ->get()
                ->groupBy(fn ($p) => $p->status?->slug ?? 'unknown')
                ->map->count()
                ->toArray();
            $data['prospekOpen'] = $statusCounts['open'] ?? 0;
            $data['prospekClosing'] = $statusCounts['closing'] ?? 0;

            // Hitung kepatuhan To Do Hari Ini (7 Tugas)
            $todayTodo = $data['todayTodo'];
            $linksCount = $todayTodo?->links->count() ?? 0;
            $linkTaskDone = $linksCount >= 12 ? 1 : 0;

            $pdfTaskKeys = [2, 3, 4, 5, 6];
            $pdfTasksFilled = $todayTodo
                ? $todayTodo->pdfs->groupBy('task')->keys()->intersect($pdfTaskKeys)->count()
                : 0;

            // Tugas 7: Prospek hari ini & update catatan
            $todayProspects = Prospect::where('marketing_user_id', $user->id)
                ->whereDate('entry_date', $today)
                ->get(['id', 'note']);

            $hasGeneralNote = ! empty(trim((string) ($todayTodo?->prospect_progress_note ?? '')));
            $task7Done = false;

            if ($todayProspects->isNotEmpty()) {
                $prospectIds = $todayProspects->pluck('id');
                $weeklyUpdatedIds = ProspectWeeklyUpdate::whereIn('prospect_id', $prospectIds)
                    ->whereNotNull('note')
                    ->where('note', '!=', '')
                    ->pluck('prospect_id')
                    ->flip()
                    ->toArray();

                $task7Done = $todayProspects->every(function ($p) use ($weeklyUpdatedIds) {
                    return ! empty(trim((string) $p->note)) || isset($weeklyUpdatedIds[$p->id]);
                });
            } elseif ($hasGeneralNote) {
                $task7Done = true;
            }

            $tasksDone = $linkTaskDone + $pdfTasksFilled + ($task7Done ? 1 : 0);
            $totalTasks = 7;
            $allDone = $tasksDone >= $totalTasks;

            $state = $allDone ? 'ok' : ($tasksDone > 0 ? 'partial' : 'empty');
            $stateLabel = $allDone ? 'Lengkap' : ($tasksDone > 0 ? 'Sebagian' : 'Belum Input');

            $data['todayTodoStats'] = [
                'links_count' => $linksCount,
                'target_links' => 12,
                'link_task_done' => $linkTaskDone,
                'pdf_tasks_filled' => $pdfTasksFilled,
                'target_pdf_tasks' => 5,
                'task7_done' => $task7Done ? 1 : 0,
                'tasks_done' => $tasksDone,
                'total_tasks' => $totalTasks,
                'pct' => min(100, round(($tasksDone / $totalTasks) * 100)),
                'state' => $state,
                'state_label' => $stateLabel,
            ];
        }

        if (in_array($role, ['manager_marketing', 'super_admin'], true)) {
            $now = now();
            $currentMonthStart = $now->copy()->startOfMonth();
            $currentMonthEnd = $now->copy()->endOfMonth();
            $prevMonthStart = $now->copy()->subMonth()->startOfMonth();
            $prevMonthEnd = $now->copy()->subMonth()->endOfMonth();

            // Total prospek bulan ini vs bulan sebelumnya
            $totalProspekMonth = Prospect::whereBetween('entry_date', [$currentMonthStart, $currentMonthEnd])->count();
            $totalProspekPrevMonth = Prospect::whereBetween('entry_date', [$prevMonthStart, $prevMonthEnd])->count();
            $diffProspek = $totalProspekMonth - $totalProspekPrevMonth;
            $percentageProspek = $totalProspekPrevMonth > 0
                ? round((($totalProspekMonth - $totalProspekPrevMonth) / $totalProspekPrevMonth) * 100, 1)
                : ($totalProspekMonth > 0 ? 100 : 0);

            // Closing bulan ini vs bulan sebelumnya
            $closingMonth = Prospect::whereBetween('entry_date', [$currentMonthStart, $currentMonthEnd])
                ->whereHas('status', fn ($q) => $q->where('slug', 'closing'))
                ->count();
            $closingPrevMonth = Prospect::whereBetween('entry_date', [$prevMonthStart, $prevMonthEnd])
                ->whereHas('status', fn ($q) => $q->where('slug', 'closing'))
                ->count();
            $percentageClosing = $closingPrevMonth > 0
                ? round((($closingMonth - $closingPrevMonth) / $closingPrevMonth) * 100, 1)
                : ($closingMonth > 0 ? 100 : 0);

            // Nominal Closing bulan ini
            $nominalMonth = Prospect::whereBetween('entry_date', [$currentMonthStart, $currentMonthEnd])
                ->whereHas('status', fn ($q) => $q->where('slug', 'closing'))
                ->sum('nominal_closing');

            // Trend 6 Bulan Terakhir untuk visualisasi grafik perbandingan
            $monthlyComparison = [];
            for ($i = 5; $i >= 0; $i--) {
                $mDate = $now->copy()->subMonths($i);
                $mStart = $mDate->copy()->startOfMonth();
                $mEnd = $mDate->copy()->endOfMonth();

                $total = Prospect::whereBetween('entry_date', [$mStart, $mEnd])->count();
                $closing = Prospect::whereBetween('entry_date', [$mStart, $mEnd])
                    ->whereHas('status', fn ($q) => $q->where('slug', 'closing'))
                    ->count();
                $open = Prospect::whereBetween('entry_date', [$mStart, $mEnd])
                    ->whereHas('status', fn ($q) => $q->where('slug', 'open'))
                    ->count();

                $monthlyComparison[] = [
                    'label' => $mDate->translatedFormat('M Y'),
                    'short_label' => $mDate->translatedFormat('M'),
                    'month_key' => $mDate->format('Y-m'),
                    'total' => $total,
                    'closing' => $closing,
                    'open' => $open,
                    'is_current' => $i === 0,
                ];
            }

            $data['totalProspekMonth'] = $totalProspekMonth;
            $data['totalProspekPrevMonth'] = $totalProspekPrevMonth;
            $data['percentageProspek'] = $percentageProspek;
            $data['diffProspek'] = $diffProspek;

            $data['closingMonth'] = $closingMonth;
            $data['closingPrevMonth'] = $closingPrevMonth;
            $data['percentageClosing'] = $percentageClosing;

            $data['nominalMonth'] = $nominalMonth;
            $data['monthlyComparison'] = $monthlyComparison;

            $marketings = User::whereHas('role', fn ($q) => $q->where('slug', 'marketing'))->orderBy('name')->get();
            $data['activeMarketings'] = $marketings->count();

            $today = today();

            // Ambil prospek hari ini untuk semua marketing
            $todayProspects = Prospect::whereDate('entry_date', $today)
                ->get(['id', 'marketing_user_id', 'entry_date', 'note']);

            $todayProspectsByUser = $todayProspects->groupBy('marketing_user_id');
            $allTodayProspectIds = $todayProspects->pluck('id');

            $allWeeklyUpdates = ProspectWeeklyUpdate::whereIn('prospect_id', $allTodayProspectIds)
                ->whereNotNull('note')
                ->where('note', '!=', '')
                ->get(['id', 'prospect_id', 'note']);

            $updatedProspectMap = [];
            foreach ($todayProspects as $tp) {
                if (! empty(trim((string) $tp->note))) {
                    $updatedProspectMap[$tp->id] = trim((string) $tp->note);
                }
            }
            foreach ($allWeeklyUpdates as $wu) {
                if (! empty(trim((string) $wu->note))) {
                    $updatedProspectMap[$wu->prospect_id] = trim((string) $wu->note);
                }
            }

            $todosToday = Todo::with(['links', 'pdfs'])
                ->whereDate('date', $today)
                ->whereHas('user.role', fn ($q) => $q->where('slug', 'marketing'))
                ->get();

            $pdfTaskKeys = [2, 3, 4, 5, 6];

            $data['todoComplianceToday'] = $marketings->map(function ($m) use ($todosToday, $todayProspectsByUser, $updatedProspectMap, $pdfTaskKeys) {
                $todo = $todosToday->firstWhere('user_id', $m->id);
                $linksCount = $todo?->links->count() ?? 0;
                $pdfsCount = $todo?->pdfs->count() ?? 0;
                $filledPdfTasks = $todo
                    ? $todo->pdfs->groupBy('task')->keys()->intersect($pdfTaskKeys)->count()
                    : 0;

                // Tugas 7: prospek yang dikirim pada tanggal ini dan seluruhnya sudah diupdate
                $dayProspects = $todayProspectsByUser->get($m->id, collect());
                $hasGeneralNote = ! empty(trim((string) ($todo?->prospect_progress_note ?? '')));

                $hasTask7 = false;
                $noteText = null;

                if ($dayProspects->isNotEmpty()) {
                    $hasTask7 = $dayProspects->every(fn ($p) => isset($updatedProspectMap[$p->id]));
                    if ($hasTask7) {
                        $firstUpdatedId = $dayProspects->first()->id;
                        $noteText = $updatedProspectMap[$firstUpdatedId] ?? 'Semua prospek terupdate';
                    }
                } elseif ($hasGeneralNote) {
                    $hasTask7 = true;
                    $noteText = $todo->prospect_progress_note;
                }

                $linkTaskDone = $linksCount >= 12 ? 1 : 0;
                $tasksDone = $linkTaskDone + $filledPdfTasks + ($hasTask7 ? 1 : 0);

                $isEmpty = ($linksCount === 0 && $pdfsCount === 0 && ! $hasTask7);
                $isComplete = ($tasksDone >= 7);

                return [
                    'name' => $m->name,
                    'links_count' => $linksCount,
                    'pdfs_count' => $pdfsCount,
                    'filled_pdf_tasks' => $filledPdfTasks,
                    'has_note' => $hasTask7,
                    'note_text' => $noteText,
                    'tasks_done' => $tasksDone,
                    'is_empty' => $isEmpty,
                    'is_complete' => $isComplete,
                ];
            });
        }

        if ($role === 'cs') {
            $data['myCreatedToday'] = Prospect::where('created_by', $user->id)
                ->whereDate('entry_date', today())
                ->count();
            $data['myCreatedMonth'] = Prospect::where('created_by', $user->id)
                ->whereMonth('entry_date', now()->month)
                ->count();
            $data['prospectLockSetting'] = ProspectLockSetting::instance();
        }

        return view('dashboard', $data);
    }
}
