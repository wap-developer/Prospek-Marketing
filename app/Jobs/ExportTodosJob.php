<?php

namespace App\Jobs;

use App\Models\Prospect;
use App\Models\ProspectWeeklyUpdate;
use App\Models\Todo;
use App\Models\TodoExport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;
use ZipArchive;

class ExportTodosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600; // 10 minutes max
    public int $tries = 1;

    public function __construct(public TodoExport $export)
    {
    }

    public function handle(): void
    {
        Carbon::setLocale('id');

        $this->export->update([
            'status' => 'processing',
            'processed_marketing' => 0,
        ]);

        $month = $this->export->month;
        $year = $this->export->year;

        $currentDate = Carbon::createFromDate($year, $month, 1);
        $monthStart = $currentDate->copy()->startOfMonth();
        $monthEnd = $currentDate->copy()->endOfMonth();
        $daysInMonth = $currentDate->daysInMonth;
        $monthLabel = $currentDate->translatedFormat('F Y');

        $queryStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $queryEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        $tasksList = [
            1 => ['title' => 'Posting 12 Link Media Sosial', 'desc' => '12 link (Instagram, TikTok, FB, Other Video)'],
            2 => ['title' => 'Broadcast & Komentar Sosial Media', 'desc' => 'Bukti PDF broadcast & komentar'],
            3 => ['title' => 'Mengiklankan Akun Instagram', 'desc' => 'Bukti PDF iklan Instagram'],
            4 => ['title' => 'DM Brosur', 'desc' => 'Bukti PDF DM brosur (Pak Sabar, Pak Henry, Marketing)'],
            5 => ['title' => 'Menyapa & Follow Up Klien Lama', 'desc' => 'Bukti PDF sapa/follow up klien lama'],
            6 => ['title' => 'Memaparkan Rencana Penjualan', 'desc' => 'Bukti PDF rencana penjualan'],
            7 => ['title' => 'Update Perkembangan Prospek', 'desc' => 'Update perkembangan prospek yang dikirim pada tanggal tersebut'],
        ];

        $marketings = User::whereHas('role', fn ($q) => $q->where('slug', 'marketing'))->orderBy('name')->get();

        if ($marketings->isEmpty()) {
            $this->export->update([
                'status' => 'failed',
                'error_message' => 'Tidak ada karyawan dengan role Marketing yang ditemukan.',
            ]);
            return;
        }

        $this->export->update(['total_marketing' => $marketings->count()]);

        // Query bulk data sekali untuk semua marketing
        $allTodos = Todo::with(['links', 'pdfs'])
            ->whereBetween('date', [$queryStart, $queryEnd])
            ->whereHas('user.role', fn ($q) => $q->where('slug', 'marketing'))
            ->get();

        $targetProspects = Prospect::with(['service', 'sender'])
            ->whereMonth('entry_date', $month)
            ->whereYear('entry_date', $year)
            ->get();

        $targetProspectsByUser = $targetProspects->groupBy('marketing_user_id');
        $allTargetProspectIds = $targetProspects->pluck('id');

        $allWeeklyUpdates = ProspectWeeklyUpdate::with(['prospect.service', 'prospect.sender'])
            ->whereIn('prospect_id', $allTargetProspectIds)
            ->whereNotNull('note')
            ->where('note', '!=', '')
            ->get();

        // Siapkan direktori penyimpanan export
        $exportDir = storage_path('app/exports');
        if (!File::isDirectory($exportDir)) {
            File::makeDirectory($exportDir, 0755, true);
        }

        $cleanMonthLabel = str_replace(' ', '_', $monthLabel);
        $zipDownloadName = 'Rekap_Todos_Marketing_' . $cleanMonthLabel . '_' . date('Ymd_His') . '.zip';
        $finalZipPath = $exportDir . DIRECTORY_SEPARATOR . $zipDownloadName;

        $zip = new ZipArchive();
        if ($zip->open($finalZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->export->update([
                'status' => 'failed',
                'error_message' => 'Gagal membuat file ZIP di storage server.',
            ]);
            return;
        }

        $createdTempFiles = [];
        $usedFileNames = [];

        foreach ($marketings as $idx => $m) {
            $userTodos = $allTodos->where('user_id', $m->id);

            $spreadsheet = $this->buildFastMarketingSpreadsheet(
                $m,
                $monthStart,
                $daysInMonth,
                $monthLabel,
                $userTodos,
                $targetProspectsByUser,
                $allWeeklyUpdates,
                $tasksList
            );

            $tempXlsx = tempnam(sys_get_temp_dir(), 'mkt_xlsx_');
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempXlsx);
            $createdTempFiles[] = $tempXlsx;

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            $cleanName = preg_replace('/[\\\\\/:\*\?"<>\|]/', '', trim((string) $m->name));
            $cleanName = trim($cleanName) ?: ('Marketing_' . ($idx + 1));
            $cleanName = str_replace(' ', '_', $cleanName);

            $baseFileName = 'Rekap_Todos_' . $cleanName . '_' . $cleanMonthLabel;
            $excelFileName = $baseFileName . '.xlsx';
            $counter = 1;
            while (in_array(strtolower($excelFileName), $usedFileNames, true)) {
                $excelFileName = $baseFileName . '_' . $counter . '.xlsx';
                $counter++;
            }
            $usedFileNames[] = strtolower($excelFileName);

            $zip->addFile($tempXlsx, $excelFileName);

            // Update progress ke database setiap marketing selesai diproses
            $this->export->increment('processed_marketing');
        }

        $zip->close();

        // Bersihkan temp files
        foreach ($createdTempFiles as $tempFile) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        $this->export->update([
            'status' => 'completed',
            'filename' => $zipDownloadName,
            'file_path' => $finalZipPath,
            'completed_at' => now(),
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('ExportTodosJob failed: ' . ($exception?->getMessage() ?? 'Unknown error'), [
            'export_id' => $this->export->id,
            'exception' => $exception,
        ]);

        $this->export->update([
            'status' => 'failed',
            'error_message' => $exception?->getMessage() ?? 'Terjadi kesalahan sistem saat proses export.',
        ]);
    }

    /**
     * Membangun spreadsheet secara cepat dengan batch styling
     */
    public function buildFastMarketingSpreadsheet(
        User $m,
        Carbon $monthStart,
        int $daysInMonth,
        string $monthLabel,
        $userTodos,
        $targetProspectsByUser,
        $allWeeklyUpdates,
        array $tasksList
    ): Spreadsheet {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $rawName = trim((string) $m->name);
        $cleanTitle = preg_replace('/[\\\\\/:\*\?\[\]\']/', '', $rawName);
        $cleanTitle = trim($cleanTitle) ?: 'Rekap To-Do';
        $sheet->setTitle(mb_substr($cleanTitle, 0, 31));
        $sheet->setShowGridLines(true);

        $userTodosByDate = $userTodos->keyBy(fn ($t) => $t->date->format('Y-m-d'));

        // Prospek marketing ini di kelompokkan berdasarkan tanggal kirim (entry_date)
        $userProspects = $targetProspectsByUser->get($m->id, collect());
        $userProspectsByDate = $userProspects->groupBy(fn ($p) => $p->entry_date ? $p->entry_date->format('Y-m-d') : '');

        // Map id prospek yang sudah terupdate
        $userWeeklyUpdates = $allWeeklyUpdates->whereIn('prospect_id', $userProspects->pluck('id'));
        $updatedProspectMap = [];
        foreach ($userProspects as $tp) {
            if (! empty(trim((string) $tp->note))) {
                $updatedProspectMap[$tp->id] = true;
            }
        }
        foreach ($userWeeklyUpdates as $wu) {
            if (! empty(trim((string) $wu->note))) {
                $updatedProspectMap[$wu->prospect_id] = true;
            }
        }

        // Hitung tugas harian
        $dailyTasks = [];
        $dailyTasksCount = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dayDate = $monthStart->copy()->day($d);
            $dateStr = $dayDate->format('Y-m-d');
            $todo = $userTodosByDate->get($dateStr);

            $linksCount = $todo?->links->count() ?? 0;
            $pdfsByTask = $todo ? $todo->pdfs->groupBy('task') : collect();

            // Tugas 7: bernilai 'X' jika ada prospek yang dikirim pada tanggal ini dan seluruhnya sudah diupdate
            $dayProspects = $userProspectsByDate->get($dateStr, collect());
            $hasTask7 = $dayProspects->isNotEmpty() && $dayProspects->every(fn ($p) => isset($updatedProspectMap[$p->id]));

            $tasksForDay = [
                1 => $linksCount >= 12,
                2 => $pdfsByTask->has(2) && $pdfsByTask->get(2)->count() > 0,
                3 => $pdfsByTask->has(3) && $pdfsByTask->get(3)->count() > 0,
                4 => $pdfsByTask->has(4) && $pdfsByTask->get(4)->count() > 0,
                5 => $pdfsByTask->has(5) && $pdfsByTask->get(5)->count() > 0,
                6 => $pdfsByTask->has(6) && $pdfsByTask->get(6)->count() > 0,
                7 => $hasTask7,
            ];
            $dailyTasks[$d] = $tasksForDay;
            $dailyTasksCount[$d] = count(array_filter($tasksForDay));
        }

        $totalFilledDays = count(array_filter($dailyTasksCount, fn ($c) => $c > 0));
        $compliancePercent = $daysInMonth > 0 ? round(($totalFilledDays / $daysInMonth) * 100, 1) : 0;

        $lastDayColStr = Coordinate::stringFromColumnIndex(3 + $daysInMonth);
        $totalXColStr = Coordinate::stringFromColumnIndex(4 + $daysInMonth);
        $pctColStr = Coordinate::stringFromColumnIndex(5 + $daysInMonth);

        // --- HEADER INFORMASI ---
        $sheet->setCellValue('A1', 'REKAP TO-DO HARIAN MARKETING');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(15)->getColor()->setARGB('FF1E293B');
        $sheet->getRowDimension(1)->setRowHeight(24);

        $sheet->setCellValue('A2', 'Periode: ' . $monthLabel);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->getColor()->setARGB('FF64748B');
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->getRowDimension(3)->setRowHeight(8);

        $sheet->setCellValue('B4', 'Nama Marketing:');
        $sheet->getStyle('B4')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF475569');
        $sheet->setCellValue('C4', $m->name);
        $sheet->getStyle('C4')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF0F172A');
        $sheet->getRowDimension(4)->setRowHeight(20);

        $sheet->setCellValue('B5', 'Email:');
        $sheet->getStyle('B5')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF475569');
        $sheet->setCellValue('C5', $m->email ?? '-');
        $sheet->getRowDimension(5)->setRowHeight(20);

        $sheet->setCellValue('B6', 'Kepatuhan Bulan Ini:');
        $sheet->getStyle('B6')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF475569');
        $complianceStatus = $compliancePercent >= 80 ? 'Sangat Baik' : ($compliancePercent >= 50 ? 'Cukup' : 'Perlu Ditingkatkan');
        $sheet->setCellValue('C6', "{$totalFilledDays} dari {$daysInMonth} Hari Aktif ({$compliancePercent}%) — {$complianceStatus}");
        $sheet->getStyle('C6')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB($compliancePercent >= 80 ? 'FF059669' : ($compliancePercent >= 50 ? 'FFD97706' : 'FFE11D48'));
        $sheet->getRowDimension(6)->setRowHeight(20);
        $sheet->getRowDimension(7)->setRowHeight(12);

        // --- MATRIKS KEPATUHAN 7 TUGAS HARIAN ---
        $sheet->setCellValue('A8', 'MATRIKS KEPATUHAN 7 TUGAS HARIAN');
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FF1E293B');
        $sheet->getRowDimension(8)->setRowHeight(22);

        // Row 9 Headers
        $sheet->setCellValue('A9', 'No');
        $sheet->setCellValue('B9', 'Uraian Tugas');
        $sheet->setCellValue('C9', 'Keterangan');
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $colLetter = Coordinate::stringFromColumnIndex(3 + $d);
            $sheet->setCellValue("{$colLetter}9", $d);
            $sheet->getColumnDimension($colLetter)->setWidth(5.2);
        }
        $sheet->setCellValue("{$totalXColStr}9", 'Total (X)');
        $sheet->setCellValue("{$pctColStr}9", '% Capaian');

        $headerRangeA = "A9:{$pctColStr}9";
        $sheet->getStyle($headerRangeA)->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRangeA)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E293B');
        $sheet->getStyle($headerRangeA)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(9)->setRowHeight(26);

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(36);
        $sheet->getColumnDimension('C')->setWidth(38);
        $sheet->getColumnDimension($totalXColStr)->setWidth(12);
        $sheet->getColumnDimension($pctColStr)->setWidth(13);
        $sheet->freezePane('D10');

        // BATCH STYLING DEFAULT RANGE UNTUK GRID TUGAS (10 - 16)
        $sheet->getStyle("A10:A16")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A10:A16")->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF1E293B');

        $sheet->getStyle("B10:B16")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("B10:B16")->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF0F172A');

        $sheet->getStyle("C10:C16")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("C10:C16")->getFont()->setSize(9)->getColor()->setARGB('FF64748B');

        $gridDataRange = "D10:{$pctColStr}16";
        $sheet->getStyle($gridDataRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        // Default cell non-X: '-' abu-abu
        $sheet->getStyle("D10:{$lastDayColStr}16")->getFont()->setSize(10)->getColor()->setARGB('FF94A3B8');
        $sheet->getStyle("D10:{$lastDayColStr}16")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');

        $sheet->getStyle("{$totalXColStr}10:{$totalXColStr}16")->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF0F172A');
        $sheet->getStyle("{$totalXColStr}10:{$totalXColStr}16")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');

        $sheet->getStyle("{$pctColStr}10:{$pctColStr}16")->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle("{$pctColStr}10:{$pctColStr}16")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');

        $styleX = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF15803D']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDCFCE7']],
        ];

        // Isi baris 10 s/d 16
        for ($taskNo = 1; $taskNo <= 7; $taskNo++) {
            $rowNum = 9 + $taskNo;
            $sheet->getRowDimension($rowNum)->setRowHeight(24);

            $tInfo = $tasksList[$taskNo];
            $sheet->setCellValue("A{$rowNum}", $taskNo);
            $sheet->setCellValue("B{$rowNum}", $tInfo['title']);
            $sheet->setCellValue("C{$rowNum}", $tInfo['desc']);

            $countXForTask = 0;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $colLetter = Coordinate::stringFromColumnIndex(3 + $d);
                $isDone = $dailyTasks[$d][$taskNo] ?? false;

                if ($isDone) {
                    $countXForTask++;
                    $sheet->setCellValue("{$colLetter}{$rowNum}", 'X');
                    $sheet->getStyle("{$colLetter}{$rowNum}")->applyFromArray($styleX);
                } else {
                    $sheet->setCellValue("{$colLetter}{$rowNum}", '-');
                }
            }

            $taskPct = $daysInMonth > 0 ? round(($countXForTask / $daysInMonth) * 100, 1) : 0;
            $sheet->setCellValue("{$totalXColStr}{$rowNum}", $countXForTask);
            $sheet->setCellValue("{$pctColStr}{$rowNum}", $taskPct . '%');
            $sheet->getStyle("{$pctColStr}{$rowNum}")->getFont()->getColor()->setARGB($taskPct >= 80 ? 'FF059669' : ($taskPct >= 50 ? 'FFD97706' : 'FFE11D48'));
        }

        // Row 17: Total Tugas Selesai Harian (0 - 7)
        $rowTotal = 17;
        $sheet->getRowDimension($rowTotal)->setRowHeight(25);
        $sheet->setCellValue("A{$rowTotal}", '');
        $sheet->setCellValue("B{$rowTotal}", 'Total Tugas Selesai');
        $sheet->setCellValue("C{$rowTotal}", 'Maksimal 7 Tugas / Hari');

        $sheet->getStyle("A{$rowTotal}:C{$rowTotal}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');
        $sheet->getStyle("B{$rowTotal}")->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF0F172A');
        $sheet->getStyle("B{$rowTotal}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("C{$rowTotal}")->getFont()->setItalic(true)->setSize(9)->getColor()->setARGB('FF64748B');
        $sheet->getStyle("C{$rowTotal}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("D{$rowTotal}:{$pctColStr}{$rowTotal}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        $styleRowTotalFull = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDCFCE7']],
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FF15803D']],
        ];
        $styleRowTotalPartial = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFEF3C7']],
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFB45309']],
        ];
        $styleRowTotalZero = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF1F5F9']],
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FF94A3B8']],
        ];

        $grandTotalX = 0;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $colLetter = Coordinate::stringFromColumnIndex(3 + $d);
            $cnt = $dailyTasksCount[$d] ?? 0;
            $grandTotalX += $cnt;

            $sheet->setCellValue("{$colLetter}{$rowTotal}", $cnt);
            if ($cnt === 7) {
                $sheet->getStyle("{$colLetter}{$rowTotal}")->applyFromArray($styleRowTotalFull);
            } elseif ($cnt > 0) {
                $sheet->getStyle("{$colLetter}{$rowTotal}")->applyFromArray($styleRowTotalPartial);
            } else {
                $sheet->getStyle("{$colLetter}{$rowTotal}")->applyFromArray($styleRowTotalZero);
            }
        }

        $maxPossibleTasks = 7 * $daysInMonth;
        $grandPct = $maxPossibleTasks > 0 ? round(($grandTotalX / $maxPossibleTasks) * 100, 1) : 0;

        $sheet->setCellValue("{$totalXColStr}{$rowTotal}", $grandTotalX);
        $sheet->setCellValue("{$pctColStr}{$rowTotal}", $grandPct . '%');
        $sheet->getStyle("{$totalXColStr}{$rowTotal}:{$pctColStr}{$rowTotal}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
        $sheet->getStyle("{$totalXColStr}{$rowTotal}:{$pctColStr}{$rowTotal}")->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF0F172A');

        // Borders Tabel Matriks A
        $tableARange = "A9:{$pctColStr}{$rowTotal}";
        $sheet->getStyle($tableARange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFCBD5E1');
        $sheet->getStyle("A9:{$pctColStr}9")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setARGB('FF64748B');
        $sheet->getStyle("A{$rowTotal}:{$pctColStr}{$rowTotal}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setARGB('FF64748B');
        $sheet->getStyle("A{$rowTotal}:{$pctColStr}{$rowTotal}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setARGB('FF64748B');

        // Catatan Kaki
        $sheet->setCellValue('B19', '* Catatan: Simbol "X" (hijau) menandakan to-do terisi. Khusus Tugas 7 (Update Perkembangan Prospek) berlaku mingguan (Senin - Minggu) bila seluruh prospek on-process telah diupdate (Aturan 2).');
        $sheet->getStyle('B19')->getFont()->setItalic(true)->setSize(9)->getColor()->setARGB('FF64748B');
        $sheet->getRowDimension(19)->setRowHeight(18);
        $sheet->getRowDimension(20)->setRowHeight(12);

        // --- BAGIAN CATATAN PERKEMBANGAN PROSPEK (TUGAS 7) ---
        $sheet->setCellValue('A21', 'CATATAN PERKEMBANGAN PROSPEK (TUGAS 7)');
        $sheet->getStyle('A21')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FF1E293B');
        $sheet->getRowDimension(21)->setRowHeight(22);

        $sheet->setCellValue('A22', 'No');
        $sheet->setCellValue('B22', 'Tanggal Dikirim CS');
        $sheet->setCellValue('C22', 'Prospek / No Telepon');
        $sheet->setCellValue('D22', 'Layanan');
        $sheet->mergeCells("E22:{$pctColStr}22");
        $sheet->setCellValue('E22', 'Isi Catatan Perkembangan Prospek');

        $headerNotesRange = "A22:{$pctColStr}22";
        $sheet->getStyle($headerNotesRange)->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerNotesRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E293B');
        $sheet->getStyle('A22:D22')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('E22')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(22)->setRowHeight(26);

        $noteRow = 23;
        $updatesByProspect = $userWeeklyUpdates->groupBy('prospect_id');
        $prospectsWithNotes = $userProspects->filter(function ($p) use ($updatesByProspect) {
            return ! empty(trim((string) $p->note)) || $updatesByProspect->has($p->id);
        })->sortBy('entry_date');

        $hasAnyNotes = $prospectsWithNotes->isNotEmpty();

        if ($hasAnyNotes) {
            $noteIdx = 1;
            foreach ($prospectsWithNotes as $p) {
                $latestUpdate = $updatesByProspect->get($p->id)?->sortByDesc('updated_at')->first();
                $noteText = trim((string) ($latestUpdate?->note ?? $p->note ?? ''));

                $dateText = $p->entry_date ? $p->entry_date->translatedFormat('d M Y') : '-';
                $phoneText = $p->client_phone ?? '-';
                $serviceText = $p->service?->name ?? '-';

                $sheet->setCellValue("A{$noteRow}", $noteIdx);
                $sheet->getStyle("A{$noteRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle("A{$noteRow}")->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF1E293B');

                $sheet->setCellValue("B{$noteRow}", $dateText);
                $sheet->getStyle("B{$noteRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle("B{$noteRow}")->getFont()->setSize(9.5)->getColor()->setARGB('FF334155');

                $sheet->setCellValue("C{$noteRow}", $phoneText);
                $sheet->getStyle("C{$noteRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle("C{$noteRow}")->getFont()->setSize(9.5)->getColor()->setARGB('FF0F172A');

                $sheet->setCellValue("D{$noteRow}", $serviceText);
                $sheet->getStyle("D{$noteRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle("D{$noteRow}")->getFont()->setSize(9.5)->getColor()->setARGB('FF475569');

                $sheet->mergeCells("E{$noteRow}:{$pctColStr}{$noteRow}");
                $sheet->setCellValue("E{$noteRow}", $noteText);
                $sheet->getStyle("E{$noteRow}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
                $sheet->getStyle("E{$noteRow}")->getFont()->setSize(10)->getColor()->setARGB('FF0F172A');

                if ($noteIdx % 2 === 0) {
                    $sheet->getStyle("A{$noteRow}:{$pctColStr}{$noteRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
                }
                $sheet->getStyle("A{$noteRow}:{$pctColStr}{$noteRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFCBD5E1');

                $lineCount = substr_count($noteText, "\n") + 1;
                $charLength = mb_strlen($noteText);
                $estimatedLines = max($lineCount, ceil($charLength / 80), 2);
                $calculatedHeight = max(36, $estimatedLines * 18 + 10);
                $sheet->getRowDimension($noteRow)->setRowHeight($calculatedHeight);

                $noteRow++;
                $noteIdx++;
            }

            $lastNoteRow = $noteRow - 1;
            $sheet->getStyle("A{$lastNoteRow}:{$pctColStr}{$lastNoteRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setARGB('FF64748B');
        } else {
            $sheet->setCellValue("A{$noteRow}", '-');
            $sheet->setCellValue("B{$noteRow}", '-');
            $sheet->setCellValue("C{$noteRow}", '-');
            $sheet->setCellValue("D{$noteRow}", '-');
            $sheet->getStyle("A{$noteRow}:D{$noteRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$noteRow}:D{$noteRow}")->getFont()->getColor()->setARGB('FF94A3B8');

            $sheet->mergeCells("E{$noteRow}:{$pctColStr}{$noteRow}");
            $sheet->setCellValue("E{$noteRow}", 'Belum ada catatan perkembangan prospek yang diisi untuk periode ini.');
            $sheet->getStyle("E{$noteRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("E{$noteRow}")->getFont()->setItalic(true)->setSize(9.5)->getColor()->setARGB('FF94A3B8');

            $sheet->getStyle("A{$noteRow}:{$pctColStr}{$noteRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            $sheet->getStyle("A{$noteRow}:{$pctColStr}{$noteRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFCBD5E1');
            $sheet->getStyle("A{$noteRow}:{$pctColStr}{$noteRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setARGB('FF64748B');
            $sheet->getRowDimension($noteRow)->setRowHeight(28);
        }

        return $spreadsheet;
    }
}
