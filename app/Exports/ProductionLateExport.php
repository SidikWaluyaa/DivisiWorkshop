<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductionLateExport implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    protected $orders;
    protected $filterStatus;
    protected $filterSearch;
    protected $dataRowCount = 0;

    public function __construct($orders, $filterStatus = null, $filterSearch = null)
    {
        $this->orders = $orders;
        $this->filterStatus = $filterStatus;
        $this->filterSearch = $filterSearch;
    }

    public function title(): string
    {
        return 'Audit Cek Fisik Lapangan';
    }

    public function array(): array
    {
        $totalSpk = $this->orders->count();
        $totalLate = $this->orders->where('warning_status', 'LATE')->count();
        $totalWarning = $this->orders->where('warning_status', 'WARNING')->count();
        $totalOnTrack = $this->orders->where('warning_status', 'ON TRACK')->count();

        $filterLabel = $this->filterStatus ? strtoupper($this->filterStatus) : 'SEMUA DATA';
        if ($this->filterSearch) {
            $filterLabel .= ' (Pencarian: "' . $this->filterSearch . '")';
        }

        $rows = [
            // Row 1: Main Header
            ['SHOE WORKSHOP — LEMBAR AUDIT & CEK FISIK PRODUKSI TERLAMBAT'],
            // Row 2: Subtitle
            ['Sistem Workshop Management • Divisi Audit Operasional & Pengendalian Produksi Workshop'],
            // Row 3: Meta Info
            ['Tanggal Cetak: ' . date('d F Y, H:i') . ' WIB  |  Filter: ' . $filterLabel . '  |  Total Antrian: ' . $totalSpk . ' Unit Sepatu'],
            // Row 4: Blank separator
            [''],
            // Row 5: KPI Summary Metrics
            [
                'TOTAL DIPERIKSA: ' . $totalSpk . ' SPK',
                '',
                'TERLAMBAT (LATE): ' . $totalLate . ' SPK',
                '',
                'WARNING (<= 5 HARI): ' . $totalWarning . ' SPK',
                '',
                'ON TRACK: ' . $totalOnTrack . ' SPK',
                '',
                '',
                '',
                '',
                'INSTRUKSI: Beri tanda centang (v) pada status fisik di workshop saat inspeksi lapangan.',
                '',
                ''
            ],
            // Row 6: Blank separator
            [''],
            // Row 7: Table Headers
            [
                'NO',
                'NO. SPK',
                'NAMA PELANGGAN',
                'MERK & TIPE SEPATU',
                'LAYANAN JASA',
                'TGL MASUK (ENTRY)',
                'ESTIMASI SELESAI',
                'SISA WAKTU / TELAT',
                'STATUS SISTEM',
                'KENDALA / CATATAN SISTEM',
                '[AUDIT] STATUS FISIK LAPANGAN',
                '[AUDIT] LOKASI RAK / STASIUN',
                '[AUDIT] CATATAN FISIK LAPANGAN'
            ]
        ];

        if ($this->orders->isEmpty()) {
            $rows[] = ['Tidak ada data produksi terlambat yang cocok dengan filter yang dipilih.', '', '', '', '', '', '', '', '', '', '', '', ''];
            $this->dataRowCount = 1;
        } else {
            $index = 1;
            foreach ($this->orders as $order) {
                // Jasa
                $services = $order->workOrderServices->pluck('service_name')->filter()->toArray();
                if (empty($services)) {
                    $services = $order->services->pluck('name')->filter()->toArray();
                }
                $servicesStr = !empty($services) ? implode(', ', $services) : '- Standar -';

                // Dates
                $entryDateStr = $order->entry_date ? $order->entry_date->format('d/m/Y') : '-';
                
                $estDate = $order->new_estimation_date ?? $order->estimation_date;
                $estDateStr = $estDate ? $estDate->format('d/m/Y') : '-';
                if ($order->new_estimation_date) {
                    $estDateStr .= ' (Revisi)';
                }

                // Days diff
                $days = $order->calendar_days_remaining;
                if ($days < 0) {
                    $diffStr = 'Terlambat ' . abs($days) . ' Hari';
                } elseif ($days == 0) {
                    $diffStr = 'Hari Ini Deadline';
                } else {
                    $diffStr = 'Sisa ' . $days . ' Hari';
                }

                // Warning status
                $statusStr = $order->warning_status ?? 'ON TRACK';

                // Description
                $descStr = $order->late_description ?? '-';
                if ($order->material_name) {
                    $descStr .= ' (Material: ' . $order->material_name . ')';
                }

                // Existing rack location if any
                $rackStr = $order->storage_rack_code ?? ($order->storageAssignments->first()?->rack?->code ?? '-');

                $rows[] = [
                    $index++,
                    $order->spk_number,
                    $order->customer_name ?? '-',
                    ($order->shoe_brand ?? '') . ' ' . ($order->shoe_type ?? '') . ($order->shoe_size ? ' (Size: ' . $order->shoe_size . ')' : ''),
                    $servicesStr,
                    $entryDateStr,
                    $estDateStr,
                    $diffStr,
                    $statusStr,
                    $descStr,
                    '[ ] ADA    [ ] TIDAK ADA    [ ] SELESAI    [ ] DI QC',
                    $rackStr !== '-' ? 'Rak: ' . $rackStr : '[ Tulis Rak/Stasiun ]',
                    '' // Blank for manual physical audit notes
                ];
            }
            $this->dataRowCount = $this->orders->count();
        }

        // Add blank row
        $rows[] = [''];

        // Add Sign-off section
        $rows[] = [
            'PETUGAS AUDIT LAPANGAN',
            '',
            '',
            '',
            '',
            '',
            '',
            'KOORDINATOR / PIC WORKSHOP PRODUKSI',
            '',
            '',
            '',
            '',
            ''
        ];
        $rows[] = [''];
        $rows[] = [''];
        $rows[] = [
            '( ........................................................... )',
            '',
            '',
            '',
            '',
            '',
            '',
            '( ........................................................... )',
            '',
            '',
            '',
            '',
            ''
        ];
        $rows[] = [
            'Tgl Cek: ____ / ____ / 2026',
            '',
            '',
            '',
            '',
            '',
            '',
            'Tgl Verifikasi: ____ / ____ / 2026',
            '',
            '',
            '',
            '',
            ''
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Set page orientation to Landscape for comfortable field audit view
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        // 1. Merge Header Rows
        $sheet->mergeCells('A1:M1');
        $sheet->mergeCells('A2:M2');
        $sheet->mergeCells('A3:M3');

        // Merge KPI Summary Box Cells
        $sheet->mergeCells('A5:B5');
        $sheet->mergeCells('C5:D5');
        $sheet->mergeCells('E5:F5');
        $sheet->mergeCells('G5:H5');
        $sheet->mergeCells('K5:M5');

        // Style Title Banner (Row 1)
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'name' => 'Calibri',
                'size' => 15,
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'], // Dark Navy
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Style Subtitle (Row 2)
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'name' => 'Calibri',
                'size' => 9.5,
                'italic' => true,
                'color' => ['rgb' => '64748B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // Style Meta Info (Row 3)
        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'name' => 'Calibri',
                'size' => 9,
                'bold' => true,
                'color' => ['rgb' => '334155'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(18);

        // Style KPI Summary Boxes (Row 5)
        $sheet->getStyle('A5:B5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('C5:D5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']], // Red
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('E5:F5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D97706']], // Orange
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('G5:H5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']], // Green
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('K5:M5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 8.5, 'italic' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']], // Yellow Note
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DASHED, 'color' => ['rgb' => 'D97706']]],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(24);

        // 2. Style Table Headers (Row 7)
        // System Data Columns (A7:J7)
        $sheet->getStyle('A7:J7')->applyFromArray([
            'font' => [
                'name' => 'Calibri',
                'size' => 9,
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Slate 800
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '475569'],
                ],
            ],
        ]);

        // Field Audit Columns (K7:M7) - Highlighted in Amber Gold
        $sheet->getStyle('K7:M7')->applyFromArray([
            'font' => [
                'name' => 'Calibri',
                'size' => 9,
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D97706'], // Amber Gold
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => 'B45309'],
                ],
            ],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(30);

        // 3. Style Data Rows (Row 8 to 7 + dataRowCount)
        $startRow = 8;
        $endRow = $startRow + max(0, $this->dataRowCount - 1);

        if ($this->dataRowCount > 0 && !$this->orders->isEmpty()) {
            $sheet->getStyle("A{$startRow}:M{$endRow}")->applyFromArray([
                'font' => [
                    'name' => 'Calibri',
                    'size' => 9,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Set specific column alignments & conditional formatting
            $currentRow = $startRow;
            foreach ($this->orders as $order) {
                $sheet->getRowDimension($currentRow)->setRowHeight(24);

                // Alignments
                $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$currentRow}")->getFont()->setBold(true);
                $sheet->getStyle("F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("L{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Field Audit Column background (Soft Gray/Cream to highlight editable fields)
                $sheet->getStyle("K{$currentRow}:M{$currentRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8FAFC'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_DOTTED,
                            'color' => ['rgb' => '94A3B8'],
                        ],
                    ],
                ]);

                // Conditional Formatting for Status Column (Col I) & Days (Col H)
                $status = $order->warning_status ?? 'ON TRACK';
                if ($status === 'LATE') {
                    $sheet->getStyle("I{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '991B1B']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']], // Soft Red
                    ]);
                    $sheet->getStyle("H{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']],
                    ]);
                } elseif ($status === 'WARNING') {
                    $sheet->getStyle("I{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '92400E']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']], // Soft Yellow
                    ]);
                    $sheet->getStyle("H{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'D97706']],
                    ]);
                } else {
                    $sheet->getStyle("I{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '065F46']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']], // Soft Green
                    ]);
                }

                $currentRow++;
            }
        }

        // 4. Style Signatures section at bottom
        $signRow1 = $endRow + 2;
        $signRow2 = $signRow1 + 3;
        $signRow3 = $signRow2 + 1;

        $sheet->mergeCells("A{$signRow1}:D{$signRow1}");
        $sheet->mergeCells("H{$signRow1}:M{$signRow1}");
        $sheet->getStyle("A{$signRow1}:M{$signRow1}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells("A{$signRow2}:D{$signRow2}");
        $sheet->mergeCells("H{$signRow2}:M{$signRow2}");
        $sheet->getStyle("A{$signRow2}:M{$signRow2}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 9.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells("A{$signRow3}:D{$signRow3}");
        $sheet->mergeCells("H{$signRow3}:M{$signRow3}");
        $sheet->getStyle("A{$signRow3}:M{$signRow3}")->applyFromArray([
            'font' => ['size' => 8.5, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        return [];
    }
}
