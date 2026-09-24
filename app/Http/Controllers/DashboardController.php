<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
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
            $data['myProspek'] = Prospect::with('status')
                ->where('marketing_user_id', $user->id)
                ->latest('entry_date')
                ->limit(5)
                ->get();
            $data['todayTodo'] = Todo::with(['links', 'pdfs'])
                ->where('user_id', $user->id)
                ->whereDate('date', today())
                ->first();
            $data['prospekToday'] = Prospect::where('marketing_user_id', $user->id)
                ->whereDate('entry_date', today())
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
            $data['prospekCancel'] = $statusCounts['cancel'] ?? 0;
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

            // Cancel bulan ini vs bulan sebelumnya
            $cancelMonth = Prospect::whereBetween('entry_date', [$currentMonthStart, $currentMonthEnd])
                ->whereHas('status', fn ($q) => $q->where('slug', 'cancel'))
                ->count();
            $cancelPrevMonth = Prospect::whereBetween('entry_date', [$prevMonthStart, $prevMonthEnd])
                ->whereHas('status', fn ($q) => $q->where('slug', 'cancel'))
                ->count();
            $percentageCancel = $cancelPrevMonth > 0
                ? round((($cancelMonth - $cancelPrevMonth) / $cancelPrevMonth) * 100, 1)
                : ($cancelMonth > 0 ? 100 : 0);

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
                $cancel = Prospect::whereBetween('entry_date', [$mStart, $mEnd])
                    ->whereHas('status', fn ($q) => $q->where('slug', 'cancel'))
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
                    'cancel' => $cancel,
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

            $data['cancelMonth'] = $cancelMonth;
            $data['cancelPrevMonth'] = $cancelPrevMonth;
            $data['percentageCancel'] = $percentageCancel;

            $data['nominalMonth'] = $nominalMonth;
            $data['monthlyComparison'] = $monthlyComparison;

            $marketings = User::whereHas('role', fn ($q) => $q->where('slug', 'marketing'))->orderBy('name')->get();
            $data['activeMarketings'] = $marketings->count();

            $today = today();
            $wYear = (int) $today->format('o');
            $wIso = (int) $today->format('W');

            // Ambil prospek on-process pada bulan ini untuk semua marketing (Aturan 2)
            $targetProspects = Prospect::whereMonth('entry_date', $today->month)
                ->whereYear('entry_date', $today->year)
                ->whereHas('status', fn ($q) => $q->where('slug', 'open'))
                ->get(['id', 'marketing_user_id']);

            $targetProspectsByUser = $targetProspects->groupBy('marketing_user_id');
            $allTargetProspectIds = $targetProspects->pluck('id');

            $allWeeklyUpdates = ProspectWeeklyUpdate::whereIn('prospect_id', $allTargetProspectIds)
                ->where('year', $wYear)
                ->where('iso_week', $wIso)
                ->whereNotNull('note')
                ->where('note', '!=', '')
                ->get(['id', 'prospect_id', 'user_id']);

            $updatesByUser = $allWeeklyUpdates->groupBy('user_id');

            $todosToday = Todo::with(['links', 'pdfs'])
                ->whereDate('date', $today)
                ->whereHas('user.role', fn ($q) => $q->where('slug', 'marketing'))
                ->get();

            $data['todoComplianceToday'] = $marketings->map(function ($m) use ($todosToday, $targetProspectsByUser, $updatesByUser) {
                $todo = $todosToday->firstWhere('user_id', $m->id);
                $linksCount = $todo?->links->count() ?? 0;
                $pdfsCount = $todo?->pdfs->count() ?? 0;
                $filledPdfTasks = $todo ? $todo->pdfs->groupBy('task')->count() : 0;

                // Aturan 2: Tugas 7 selesai jika ada prospek dan seluruh prospek on-process telah diupdate di minggu ini
                $userTargetIds = $targetProspectsByUser->get($m->id, collect())->pluck('id');
                $targetCount = $userTargetIds->count();
                $hasTask7 = false;
                if ($targetCount > 0) {
                    $userUpdates = $updatesByUser->get($m->id, collect());
                    $distinctUpdatedCount = $userUpdates->pluck('prospect_id')->unique()->count();
                    $hasTask7 = ($distinctUpdatedCount >= $targetCount);
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
                    'note_text' => $hasTask7 ? 'Semua prospek terupdate' : null,
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
        }

        return view('dashboard', $data);
    }
}
