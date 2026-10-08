<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Prospect;
use App\Models\ProspectLockSetting;
use App\Models\ProspectSource;
use App\Models\ProspectStatus;
use App\Models\ProspectWeeklyUpdate;
use App\Models\Sender;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProspectController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $role = $user->role?->slug;

        $query = Prospect::query()
            ->with(['service', 'sender', 'group', 'source', 'marketing', 'status', 'creator', 'weeklyUpdates.user'])
            ->latest('entry_date')
            ->latest('entry_time');

        // Marketing: hanya prospek miliknya
        if ($role === 'marketing') {
            $query->where('marketing_user_id', $user->id);
        }

        $startDate = $request->filled('start_date') ? $request->input('start_date') : null;
        $endDate = $request->filled('end_date') ? $request->input('end_date') : null;

        // Tanggal awal dan tanggal akhir harus keduanya dipilih untuk filter rentang tanggal
        if ($startDate && $endDate) {
            $start = min($startDate, $endDate);
            $end = max($startDate, $endDate);
            $query->whereDate('entry_date', '>=', $start)
                  ->whereDate('entry_date', '<=', $end);
        }

        if (! $startDate && ! $endDate) {
            if ($request->filled('month')) {
                $query->whereMonth('entry_date', $request->integer('month'));
                if ($request->filled('year')) {
                    $query->whereYear('entry_date', $request->integer('year'));
                } else {
                    $query->whereYear('entry_date', (int) now()->year);
                }
            } elseif ($request->filled('year')) {
                $query->whereYear('entry_date', $request->integer('year'));
            }
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->integer('status_id'));
        }
        if ($request->filled('marketing_user_id') && in_array($role, ['manager_marketing', 'super_admin'], true)) {
            $query->where('marketing_user_id', $request->integer('marketing_user_id'));
        }
        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->where('client_phone', 'like', $term);
        }

        // Scope pemisah Grup Iklan vs Selain Grup Iklan
        $isIklanScope = fn ($q) => $q->whereHas('group', fn ($g) => $g->whereRaw('LOWER(name) LIKE ?', ['%iklan%']));
        $isNonIklanScope = fn ($q) => $q->whereDoesntHave('group', fn ($g) => $g->whereRaw('LOWER(name) LIKE ?', ['%iklan%']));

        // Query Non-Iklan (Tabel Atas)
        $nonIklanQuery = (clone $query)->tap($isNonIklanScope);
        $nonIklanTotal = (clone $nonIklanQuery)->count();
        $nonIklanOpen = (clone $nonIklanQuery)->whereHas('status', fn ($s) => $s->where('slug', 'open'))->count();
        $nonIklanClosing = (clone $nonIklanQuery)->whereHas('status', fn ($s) => $s->where('slug', 'closing'))->count();
        $prospects = (clone $nonIklanQuery)->paginate(15, ['*'], 'page')->withQueryString();

        // Query Iklan (Tabel Bawah Khusus Grup Iklan)
        $iklanQuery = (clone $query)->tap($isIklanScope);
        $iklanTotal = (clone $iklanQuery)->count();
        $iklanOpen = (clone $iklanQuery)->whereHas('status', fn ($s) => $s->where('slug', 'open'))->count();
        $iklanClosing = (clone $iklanQuery)->whereHas('status', fn ($s) => $s->where('slug', 'closing'))->count();
        $iklanProspects = (clone $iklanQuery)->paginate(15, ['*'], 'page_iklan')->withQueryString();

        $totalAll = $nonIklanTotal + $iklanTotal;

        return view('prospects.index', [
            'prospects' => $prospects,
            'nonIklanStats' => [
                'total' => $nonIklanTotal,
                'open' => $nonIklanOpen,
                'closing' => $nonIklanClosing,
            ],
            'iklanProspects' => $iklanProspects,
            'iklanStats' => [
                'total' => $iklanTotal,
                'open' => $iklanOpen,
                'closing' => $iklanClosing,
            ],
            'totalAll' => $totalAll,
            'statuses' => ProspectStatus::where('slug', '!=', 'cancel')->orderBy('id')->get(),
            'marketings' => User::whereHas('role', fn ($q) => $q->where('slug', 'marketing'))->orderBy('name')->get(),
            'role' => $role,
            'lockSetting' => ProspectLockSetting::instance(),
            'startDate' => $request->input('start_date'),
            'endDate' => $request->input('end_date'),
            'month' => $request->filled('month') ? $request->integer('month') : null,
            'year' => $request->filled('year') ? $request->integer('year') : null,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        Carbon::setLocale('id');

        $user = $request->user();
        $role = $user->role?->slug;

        $query = Prospect::query()
            ->with(['service', 'sender', 'group', 'source', 'marketing', 'status', 'creator'])
            ->latest('entry_date')
            ->latest('entry_time');

        // Marketing: hanya prospek miliknya
        if ($role === 'marketing') {
            $query->where('marketing_user_id', $user->id);
        }

        $startDate = $request->filled('start_date') ? $request->input('start_date') : null;
        $endDate = $request->filled('end_date') ? $request->input('end_date') : null;
        $month = $request->filled('month') ? $request->integer('month') : null;
        $year = $request->filled('year') ? $request->integer('year') : ($month ? (int) now()->year : null);

        // Tanggal awal dan tanggal akhir harus keduanya dipilih untuk filter rentang tanggal
        if ($startDate && $endDate) {
            $start = min($startDate, $endDate);
            $end = max($startDate, $endDate);
            $query->whereDate('entry_date', '>=', $start);
            $query->whereDate('entry_date', '<=', $end);
        }

        if (! $startDate && ! $endDate) {
            if ($month) {
                $query->whereMonth('entry_date', $month);
                if ($year) {
                    $query->whereYear('entry_date', $year);
                }
            } elseif ($year) {
                $query->whereYear('entry_date', $year);
            }
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->integer('status_id'));
        }
        if ($request->filled('marketing_user_id') && in_array($role, ['manager_marketing', 'super_admin'], true)) {
            $query->where('marketing_user_id', $request->integer('marketing_user_id'));
        }
        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->where('client_phone', 'like', $term);
        }

        if ($request->filled('group_type')) {
            $groupType = $request->string('group_type')->toString();
            if ($groupType === 'iklan') {
                $query->whereHas('group', fn ($g) => $g->whereRaw('LOWER(name) LIKE ?', ['%iklan%']));
            } elseif ($groupType === 'non_iklan') {
                $query->whereDoesntHave('group', fn ($g) => $g->whereRaw('LOWER(name) LIKE ?', ['%iklan%']));
            }
        }

        $prospects = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Prospek');
        $sheet->setShowGridLines(true);

        $categorySuffix = match ($request->string('group_type')->toString()) {
            'iklan' => ' (GRUP IKLAN)',
            'non_iklan' => ' (NON-IKLAN)',
            default => '',
        };
        $filenameCategory = match ($request->string('group_type')->toString()) {
            'iklan' => 'Iklan_',
            'non_iklan' => 'Non_Iklan_',
            default => '',
        };

        // Header Title
        if ($startDate && $endDate) {
            $start = min($startDate, $endDate);
            $end = max($startDate, $endDate);
            $startFormatted = Carbon::parse($start)->format('d_m_Y');
            $endFormatted = Carbon::parse($end)->format('d_m_Y');
            $startHuman = Carbon::parse($start)->translatedFormat('d M Y');
            $endHuman = Carbon::parse($end)->translatedFormat('d M Y');
            $titleText = 'PROSPEK MARKETING HIVE FIVE' . $categorySuffix . ' PERIODE ' . strtoupper($startHuman) . ' S/D ' . strtoupper($endHuman);
            $filenamePeriod = $filenameCategory . $startFormatted . '_sd_' . $endFormatted;
        } elseif ($month && $year) {
            $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
            $titleText = 'PROSPEK MARKETING HIVE FIVE' . $categorySuffix . ' PERIODE ' . strtoupper($monthName);
            $filenamePeriod = $filenameCategory . str_replace(' ', '_', $monthName);
        } elseif ($year) {
            $titleText = 'PROSPEK MARKETING HIVE FIVE' . $categorySuffix . ' PERIODE TAHUN ' . $year;
            $filenamePeriod = $filenameCategory . 'Tahun_' . $year;
        } else {
            $titleText = 'PROSPEK MARKETING HIVE FIVE' . $categorySuffix . ' PERIODE SEMUA DATA';
            $filenamePeriod = $filenameCategory . 'Semua_Periode';
        }

        // Row 1: Title
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', $titleText);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11.5)->getColor()->setARGB('FF1E293B');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Row 2: Kolom Header
        $headers = [
            'A2' => ['col' => 'A', 'title' => 'No', 'width' => 6, 'align' => Alignment::HORIZONTAL_CENTER],
            'B2' => ['col' => 'B', 'title' => 'Nomor Telfon Client', 'width' => 22, 'align' => Alignment::HORIZONTAL_LEFT],
            'C2' => ['col' => 'C', 'title' => 'Layanan/ Jasa', 'width' => 24, 'align' => Alignment::HORIZONTAL_LEFT],
            'D2' => ['col' => 'D', 'title' => 'Tanggal Masuk Prospek', 'width' => 22, 'align' => Alignment::HORIZONTAL_CENTER],
            'E2' => ['col' => 'E', 'title' => 'Jam', 'width' => 10, 'align' => Alignment::HORIZONTAL_CENTER],
            'F2' => ['col' => 'F', 'title' => 'Pengirim Prospek', 'width' => 20, 'align' => Alignment::HORIZONTAL_LEFT],
            'G2' => ['col' => 'G', 'title' => 'Group', 'width' => 18, 'align' => Alignment::HORIZONTAL_LEFT],
            'H2' => ['col' => 'H', 'title' => 'Sumber Prospek', 'width' => 20, 'align' => Alignment::HORIZONTAL_LEFT],
            'I2' => ['col' => 'I', 'title' => 'Nama Marketing', 'width' => 22, 'align' => Alignment::HORIZONTAL_LEFT],
            'J2' => ['col' => 'J', 'title' => 'Status Prospek', 'width' => 18, 'align' => Alignment::HORIZONTAL_CENTER],
            'K2' => ['col' => 'K', 'title' => 'Tanggal & Waktu Closing', 'width' => 24, 'align' => Alignment::HORIZONTAL_CENTER],
            'L2' => ['col' => 'L', 'title' => 'KETERANGAN', 'width' => 36, 'align' => Alignment::HORIZONTAL_LEFT],
        ];

        foreach ($headers as $cell => $info) {
            $sheet->setCellValue($cell, $info['title']);
            $sheet->getColumnDimension($info['col'])->setWidth($info['width']);
        }

        $headerRange = 'A2:L2';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E293B');
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        foreach ($headers as $cell => $info) {
            $sheet->getStyle($cell)->getAlignment()->setHorizontal($info['align']);
        }
        $sheet->getRowDimension(2)->setRowHeight(28);

        $sheet->freezePane('C3');

        $rowNum = 3;

        foreach ($prospects as $idx => $p) {
            $sheet->getRowDimension($rowNum)->setRowHeight(22);

            // A: No
            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

            // B: Nomor Telfon Client (explicit text)
            $sheet->setCellValueExplicit("B{$rowNum}", $p->client_phone ?? '-', DataType::TYPE_STRING);
            $sheet->getStyle("B{$rowNum}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("B{$rowNum}")->getFont()->setBold(true)->getColor()->setARGB('FF0F172A');

            // C: Layanan/ Jasa
            $sheet->setCellValue("C{$rowNum}", $p->service->name ?? '-');
            $sheet->getStyle("C{$rowNum}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // D: Tanggal Masuk Prospek
            $sheet->setCellValue("D{$rowNum}", $p->entry_date ? $p->entry_date->translatedFormat('d/m/Y') : '-');
            $sheet->getStyle("D{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

            // E: Jam
            $sheet->setCellValue("E{$rowNum}", $p->entry_time ? substr($p->entry_time, 0, 5) : '-');
            $sheet->getStyle("E{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

            // F: Pengirim Prospek
            $sheet->setCellValue("F{$rowNum}", $p->sender->name ?? '-');
            $sheet->getStyle("F{$rowNum}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // G: Group
            $sheet->setCellValue("G{$rowNum}", $p->group->name ?? '-');
            $sheet->getStyle("G{$rowNum}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // H: Sumber Prospek
            $sheet->setCellValue("H{$rowNum}", $p->source->name ?? '-');
            $sheet->getStyle("H{$rowNum}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // I: Nama Marketing
            $sheet->setCellValue("I{$rowNum}", $p->marketing->name ?? '-');
            $sheet->getStyle("I{$rowNum}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // J: Progress Prospek
            $statusName = $p->status->name ?? '-';
            $sheet->setCellValue("J{$rowNum}", $statusName);
            $sheet->getStyle("J{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("J{$rowNum}")->getFont()->setBold(true);

            $statusSlug = $p->status->slug ?? '';
            if ($statusSlug === 'closing') {
                $sheet->getStyle("J{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDCFCE7');
                $sheet->getStyle("J{$rowNum}")->getFont()->getColor()->setARGB('FF15803D');
            } elseif ($statusSlug === 'open') {
                $sheet->getStyle("J{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDBEAFE');
                $sheet->getStyle("J{$rowNum}")->getFont()->getColor()->setARGB('FF1D4ED8');
            } elseif ($statusSlug === 'cancel') {
                $sheet->getStyle("J{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFEE2E2');
                $sheet->getStyle("J{$rowNum}")->getFont()->getColor()->setARGB('FFB91C1C');
            }

            // K: Tanggal & Waktu Closing
            $closingText = $p->closed_at ? $p->closed_at->translatedFormat('d/m/Y H:i') : '-';
            $sheet->setCellValue("K{$rowNum}", $closingText);
            $sheet->getStyle("K{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

            // L: KETERANGAN (gabungan nominal closing jika ada + catatan)
            $keteranganParts = [];
            if ($p->nominal_closing && (float) $p->nominal_closing > 0) {
                $keteranganParts[] = 'Closing: Rp ' . number_format((float) $p->nominal_closing, 0, ',', '.');
            }
            if (!empty(trim($p->note ?? ''))) {
                $keteranganParts[] = trim($p->note);
            }
            $keterangan = !empty($keteranganParts) ? implode(' - ', $keteranganParts) : '-';
            $sheet->setCellValue("L{$rowNum}", $keterangan);
            $sheet->getStyle("L{$rowNum}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // Alternating row background
            if ($idx % 2 === 1) {
                $sheet->getStyle("A{$rowNum}:L{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $rowNum++;
        }

        // Summary row (Total Prospek)
        $summaryRow = $rowNum;
        $sheet->getRowDimension($summaryRow)->setRowHeight(26);
        $sheet->mergeCells("A{$summaryRow}:I{$summaryRow}");
        $sheet->setCellValue("A{$summaryRow}", 'TOTAL DATA: ' . $prospects->count() . ' PROSPEK');
        $sheet->getStyle("A{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$summaryRow}")->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF1E293B');

        $closingCount = $prospects->filter(fn ($p) => ($p->status->slug ?? '') === 'closing')->count();
        $openCount = $prospects->filter(fn ($p) => ($p->status->slug ?? '') === 'open')->count();
        $cancelCount = $prospects->filter(fn ($p) => ($p->status->slug ?? '') === 'cancel')->count();

        $sheet->setCellValue("J{$summaryRow}", "Closing: {$closingCount} | Open: {$openCount} | Cancel: {$cancelCount}");
        $sheet->mergeCells("J{$summaryRow}:L{$summaryRow}");
        $sheet->getStyle("J{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("J{$summaryRow}")->getFont()->setBold(true)->setSize(9.5)->getColor()->setARGB('FF475569');
        $sheet->getStyle("A{$summaryRow}:L{$summaryRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');

        // Borders
        $sheet->getStyle("A2:L{$summaryRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFCBD5E1');
        $sheet->getStyle("A2:L2")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setARGB('FF64748B');
        $sheet->getStyle("A{$summaryRow}:L{$summaryRow}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setARGB('FF64748B');
        $sheet->getStyle("A{$summaryRow}:L{$summaryRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setARGB('FF64748B');

        $filename = 'Data_Prospek_' . $filenamePeriod . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $role = $request->user()->role?->slug;
        abort_unless(in_array($role, ['cs', 'super_admin'], true), 403);

        $lockSetting = ProspectLockSetting::instance();
        if ($role === 'cs' && $lockSetting->is_locked) {
            $msg = $lockSetting->reason ?: 'Input prospek sedang dikunci. Jam operasional pembuatan prospek baru adalah pukul 06:00 - 22:00 WIB.';
            return redirect()->route('prospects.index')->with('error', $msg);
        }

        return view('prospects.create', array_merge($this->formOptions(), [
            'lockSetting' => $lockSetting,
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $role = $request->user()->role?->slug;
        abort_unless(in_array($role, ['cs', 'super_admin'], true), 403);

        $lockSetting = ProspectLockSetting::instance();
        if ($role === 'cs' && $lockSetting->is_locked) {
            $msg = $lockSetting->reason ?: 'Input prospek sedang dikunci. Jam operasional pembuatan prospek baru adalah pukul 06:00 - 22:00 WIB.';
            return redirect()->route('prospects.index')->with('error', $msg);
        }

        $phoneDigits = preg_replace('/\D+/', '', (string) $request->input('client_phone', ''));

        // Deteksi double-submit jika prospek dengan nomor yang sama baru saja dibuat oleh user ini dalam 10 detik terakhir
        if ($phoneDigits !== '') {
            $variants = self::getPhoneVariants($phoneDigits);
            $recentlyCreated = Prospect::whereIn('client_phone', $variants)
                ->where('created_by', $request->user()->id)
                ->where('created_at', '>=', now()->subSeconds(10))
                ->latest('id')
                ->first();

            if ($recentlyCreated) {
                return redirect()->route('prospects.index')->with('status', 'Prospek berhasil dibuat.');
            }
        }

        $lock = null;
        if ($phoneDigits !== '') {
            try {
                $lock = Cache::lock('store_prospect_' . $phoneDigits, 5);
                $lock->block(3);
            } catch (\Throwable $e) {
                $lock = null;
            }
        }

        try {
            // Cek ulang setelah lock berhasil didapatkan
            if ($phoneDigits !== '') {
                $variants = self::getPhoneVariants($phoneDigits);
                $recentlyCreated = Prospect::whereIn('client_phone', $variants)
                    ->where('created_by', $request->user()->id)
                    ->where('created_at', '>=', now()->subSeconds(10))
                    ->latest('id')
                    ->first();

                if ($recentlyCreated) {
                    return redirect()->route('prospects.index')->with('status', 'Prospek berhasil dibuat.');
                }
            }

            $data = $this->validateProspect($request);
            $data['created_by'] = $request->user()->id;
            $data['status_id'] = ProspectStatus::where('slug', 'open')->value('id');

            if (empty($data['source_id'])) {
                $defaultSource = ProspectSource::firstOrCreate(['name' => '-']);
                $data['source_id'] = $defaultSource->id;
            }

            Prospect::create($data);
        } finally {
            if ($lock) {
                try {
                    $lock->release();
                } catch (\Throwable $e) {
                    // Ignore release errors
                }
            }
        }

        return redirect()->route('prospects.index')->with('status', 'Prospek berhasil dibuat.');
    }

    public function edit(Request $request, Prospect $prospect): View
    {
        $this->authorizeProspectAccess($request, $prospect, editable: true);

        $now = Carbon::now();
        $weeklyUpdates = $prospect->weeklyUpdates()->with('user')->limit(8)->get();
        $currentMonth = (int) $now->month;
        $currentYear = (int) $now->year;
        // Determine week-of-month (1..4) for today
        $dom = (int) $now->day;
        $currentWeekOfMonth = (int) ceil($dom / 7);
        if ($currentWeekOfMonth > 4) {
            $currentWeekOfMonth = 4;
        }

        // Build keyed collection for fast lookup
        $updatesByWeek = $weeklyUpdates->keyBy(fn ($u) => $u->week_of_month);

        // Access flags from admin (which weeks are open for this prospect, current month)
        $accessByWeek = $prospect->weeklyAccess()
            ->where('month', $currentMonth)
            ->get()
            ->keyBy('week_of_month');

        return view('prospects.edit', array_merge(
            [
                'prospect' => $prospect,
                'role' => $request->user()->role?->slug,
                'weeklyUpdates' => $weeklyUpdates,
                'updatesByWeek' => $updatesByWeek,
                'accessByWeek' => $accessByWeek,
                'currentMonth' => $currentMonth,
                'currentYear' => $currentYear,
                'currentWeekOfMonth' => $currentWeekOfMonth,
            ],
            $this->formOptions(),
        ));
    }

    public function update(Request $request, Prospect $prospect): RedirectResponse
    {
        $this->authorizeProspectAccess($request, $prospect, editable: true);

        $data = $this->validateProspect($request, partial: true, ignoreProspectId: $prospect->id);

        if (array_key_exists('source_id', $data) && empty($data['source_id'])) {
            $defaultSource = ProspectSource::firstOrCreate(['name' => '-']);
            $data['source_id'] = $defaultSource->id;
        }

        if (array_key_exists('status_id', $data)) {
            $statusSlug = ProspectStatus::find($data['status_id'])?->slug;

            if ($statusSlug === 'cancel') {
                $clientPhone = $prospect->client_phone;
                $prospect->delete();

                return redirect()->route('prospects.index')->with('status', "Prospek {$clientPhone} berhasil dicancel dan dihapus dari sistem.");
            }

            if ($statusSlug === 'closing' && empty($data['closed_at'])) {
                $data['closed_at'] = now();
            }
            if ($statusSlug !== 'closing') {
                $data['closed_at'] = null;
            }
        }

        $prospect->update($data);

        // Save weekly update if submitted (per-week forms have weekly_year/month/week hidden)
        if ($request->filled('weekly_year') && $request->filled('weekly_month') && $request->filled('weekly_week')) {
            $weekOfMonth = max(1, min(4, (int) $request->input('weekly_week')));
            $month = max(1, min(12, (int) $request->input('weekly_month')));
            $year = (int) $request->input('weekly_year');
            $noteField = 'weekly_note_'.$weekOfMonth;
            if ($request->filled($noteField)) {
                ProspectWeeklyUpdate::updateOrCreate(
                    ['prospect_id' => $prospect->id, 'month' => $month, 'week_of_month' => $weekOfMonth],
                    [
                        'user_id' => $request->user()->id,
                        'note' => $request->string($noteField),
                    ],
                );
            }
        }

        if ($request->user()->role?->slug === 'cs') {
            return redirect()->route('prospects.index')->with('status', 'Data prospek berhasil diperbarui.');
        }

        return redirect()->route('prospects.edit', $prospect)->with('status', 'Prospek diperbarui.');
    }

    public function destroy(Request $request, Prospect $prospect): RedirectResponse
    {
        abort_unless(in_array($request->user()->role?->slug, ['manager_marketing', 'super_admin'], true), 403);
        $prospect->delete();

        return redirect()->route('prospects.index')->with('status', 'Prospek dihapus.');
    }

    public function toggleLock(Request $request): RedirectResponse
    {
        $role = $request->user()->role?->slug;
        abort_unless(in_array($role, ['manager_marketing', 'super_admin'], true), 403);

        $lockSetting = ProspectLockSetting::instance();
        if ($lockSetting->is_locked) {
            $lockSetting->unlock($request->user()->id);
            $msg = 'Kunci input prospek berhasil dibuka. CS dapat menambahkan prospek baru kembali.';
        } else {
            $reason = trim((string) $request->input('reason', ''));
            if ($reason === '') {
                $reason = 'Input prospek ditutup manual oleh ' . ($role === 'super_admin' ? 'Super Admin' : 'Manager Marketing') . '.';
            }
            $lockSetting->lock($request->user()->id, $reason);
            $msg = 'Input prospek berhasil dikunci. CS tidak dapat menambahkan prospek baru saat ini.';
        }

        return back()->with('status', $msg);
    }

    public function checkPhone(Request $request): JsonResponse
    {
        $role = $request->user()->role?->slug;
        abort_unless(in_array($role, ['cs', 'super_admin'], true), 403);

        $phone = (string) $request->input('phone', '');
        $ignoreId = $request->filled('ignore_id') ? (int) $request->input('ignore_id') : null;
        $existing = $this->findExistingProspectByPhone($phone, $ignoreId);

        if (! $existing) {
            return response()->json(['exists' => false]);
        }

        $marketingName = $existing->marketing?->name ?? 'Marketing Lain';
        $serviceName = $existing->service?->name;
        $statusName = $existing->status?->name;
        $entryDate = $existing->entry_date ? $existing->entry_date->translatedFormat('d M Y') : null;

        $message = "Nomor prospek ini sudah ada di marketing {$marketingName}";
        if ($serviceName) {
            $message .= " (Layanan: {$serviceName}";
            if ($statusName) {
                $message .= ", Status: {$statusName}";
            }
            if ($entryDate) {
                $message .= ", Masuk: {$entryDate}";
            }
            $message .= ")";
        }
        $message .= '.';

        return response()->json([
            'exists' => true,
            'message' => $message,
            'marketing' => $marketingName,
            'service' => $serviceName,
            'status' => $statusName,
            'entry_date' => $entryDate,
        ]);
    }

    private function validateProspect(Request $request, bool $partial = false, ?int $ignoreProspectId = null): array
    {
        if ($request->has('nominal_closing')) {
            $rawNominal = $request->input('nominal_closing');
            if (is_string($rawNominal)) {
                $cleanNominal = preg_replace('/[^0-9]/', '', $rawNominal);
                $request->merge(['nominal_closing' => $cleanNominal !== '' ? (float) $cleanNominal : null]);
            }
        }

        $required = $partial ? 'sometimes' : 'required';

        $validator = Validator::make($request->all(), [
            'client_phone' => [$required, 'string', 'max:32', 'regex:/^[0-9]+$/'],
            'service_id' => [$required, 'exists:services,id'],
            'entry_date' => [$required, 'date'],
            'entry_time' => [$required, 'date_format:H:i'],
            'sender_id' => [$required, 'exists:senders,id'],
            'group_id' => [$required, 'exists:groups,id'],
            'source_id' => ['nullable', 'exists:prospect_sources,id'],
            'marketing_user_id' => [$required, 'exists:users,id'],
            'status_id' => [$partial ? 'sometimes' : 'nullable', 'exists:prospect_statuses,id'],
            'nominal_closing' => [$partial ? 'sometimes' : 'nullable', 'nullable', 'numeric', 'min:0'],
            'note' => [$partial ? 'sometimes' : 'nullable', 'nullable', 'string', 'max:2000'],
            'closed_at' => [$partial ? 'sometimes' : 'nullable', 'nullable', 'date'],
        ], [
            'client_phone.required' => 'Nomor Telepon / WhatsApp wajib diisi.',
            'client_phone.regex' => 'Nomor telepon hanya boleh berisi angka (0-9).',
            'client_phone.max' => 'Nomor telepon maksimal 32 digit.',
        ]);

        $validator->after(function ($v) use ($request, $ignoreProspectId) {
            $phone = (string) $request->input('client_phone', '');
            if ($phone !== '') {
                $existing = $this->findExistingProspectByPhone($phone, $ignoreProspectId);
                if ($existing) {
                    $marketingName = $existing->marketing?->name ?? 'Marketing Lain';
                    $serviceName = $existing->service?->name;
                    $statusName = $existing->status?->name;
                    $entryDate = $existing->entry_date ? $existing->entry_date->translatedFormat('d M Y') : null;

                    $msg = "Nomor prospek ini sudah ada di marketing {$marketingName}";
                    if ($serviceName) {
                        $msg .= " (Layanan: {$serviceName}";
                        if ($statusName) {
                            $msg .= ", Status: {$statusName}";
                        }
                        if ($entryDate) {
                            $msg .= ", Masuk: {$entryDate}";
                        }
                        $msg .= ")";
                    }
                    $msg .= '.';

                    $v->errors()->add('client_phone', $msg);
                }
            }
        });

        return $validator->validate();
    }

    public static function getPhoneVariants(string $phone): array
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (empty($digits)) {
            return [];
        }

        $variants = [$digits];

        if (str_starts_with($digits, '62')) {
            $withoutCountry = substr($digits, 2);
            if ($withoutCountry !== '') {
                $variants[] = '0' . $withoutCountry;
                $variants[] = $withoutCountry;
            }
        } elseif (str_starts_with($digits, '0')) {
            $withoutZero = substr($digits, 1);
            if ($withoutZero !== '') {
                $variants[] = '62' . $withoutZero;
                $variants[] = $withoutZero;
            }
        } else {
            $variants[] = '0' . $digits;
            $variants[] = '62' . $digits;
        }

        return array_values(array_unique(array_filter($variants)));
    }

    private function findExistingProspectByPhone(string $phone, ?int $ignoreProspectId = null): ?Prospect
    {
        $variants = self::getPhoneVariants($phone);
        if (empty($variants)) {
            return null;
        }

        $query = Prospect::with(['marketing', 'service', 'status'])
            ->whereIn('client_phone', $variants);

        if ($ignoreProspectId !== null) {
            $query->where('id', '!=', $ignoreProspectId);
        }

        return $query->latest('entry_date')->latest('entry_time')->first();
    }

    private function formOptions(): array
    {
        return [
            'services' => Service::orderBy('name')->get(),
            'senders' => Sender::orderBy('name')->get(),
            'groups' => Group::orderBy('name')->get(),
            'sources' => ProspectSource::orderBy('name')->get(),
            'statuses' => ProspectStatus::orderBy('id')->get(),
            'marketings' => User::whereHas('role', fn ($q) => $q->where('slug', 'marketing'))->orderBy('name')->get(),
        ];
    }

    private function authorizeProspectAccess(Request $request, Prospect $prospect, bool $editable): void
    {
        $user = $request->user();
        $role = $user->role?->slug;

        if (in_array($role, ['manager_marketing', 'super_admin'], true)) {
            return;
        }
        if ($role === 'cs') {
            return; // CS boleh lihat & update semua
        }
        if ($role === 'marketing') {
            abort_unless($prospect->marketing_user_id === $user->id, 403);

            return;
        }
        abort(403);
    }
}
