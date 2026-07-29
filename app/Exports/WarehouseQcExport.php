<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WarehouseQcExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $summary;
    protected $startDate;
    protected $endDate;

    public function __construct($summary, $startDate, $endDate)
    {
        $this->summary = $summary;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $rangeLabel = $this->startDate->format('d/m/Y') . ' s/d ' . $this->endDate->format('d/m/Y');
        $metrics = $this->summary['metrics'] ?? [];
        $items = $this->summary['items'] ?? [];

        $totalQc = $metrics['total_items_in_qc'] ?? 0;
        $overdueQc = $metrics['overdue_items_count'] ?? 0;
        $upcomingQc = $metrics['upcoming_items_count'] ?? 0;

        $rows = [
            // Row 1: Main Header
            ['SHOE WORKSHOP - LAPORAN QC'],
            // Row 2: Filter Info
            ['Periode Laporan:', $rangeLabel],
            // Row 3: Blank separator
            [''],
            
            // Row 4: Section 1 Header
            ['RINGKASAN METRIK QUALITY CONTROL (QC)'],
            // Row 5: Column Headers
            ['Nama Metrik', 'Nilai Metrik', 'Keterangan'],
            // Row 6 - 8: Metrics
            ['Total SPK Sedang di QC', $totalQc . ' SPK', 'Sedang dalam proses QC'],
            ['Terlewat Estimasi', $overdueQc . ' SPK', 'Melewati target estimasi selesai'],
            ['Mendekati Estimasi (≤ 2 Hari)', $upcomingQc . ' SPK', 'Segera jatuh tempo'],
            
            // Row 9: Blank separator
            [''],
            
            // Row 10: Section 2 Header
            ['DAFTAR SPK SEDANG DI QC'],
            // Row 11: Table Headers
            ['No. SPK', 'Pelanggan', 'Detail Sepatu', 'Tanggal Masuk QC', 'Estimasi Selesai', 'Sisa Waktu', 'Status']
        ];

        // Row 12+: Items
        if (empty($items)) {
            $rows[] = ['Tidak ada sepatu terdata di tahap QC.', '', '', '', '', '', ''];
        } else {
            foreach ($items as $item) {
                $shoeDetail = $item['shoe_brand'] . ' ' . $item['shoe_type'];
                
                $timeInQc = ($item['days_in_qc'] ?? 0) . ' Hari';
                $enteredAtStr = ($item['entered_qc_at_formatted'] ?? '-') . ' (' . $timeInQc . ')';

                $timeDiff = '-';
                if ($item['has_estimation']) {
                    if ($item['is_overdue']) {
                        $timeDiff = 'Kelewat ' . $item['days_diff'] . ' Hari';
                    } else {
                        $timeDiff = $item['days_diff'] . ' Hari Lagi';
                    }
                }

                $statusLabel = 'On Track';
                if ($item['is_overdue']) {
                    $statusLabel = 'Overdue';
                } elseif ($item['is_upcoming']) {
                    $statusLabel = 'Due Soon';
                }

                $rows[] = [
                    $item['spk_number'],
                    $item['customer_name'] ?? 'N/A',
                    $shoeDetail,
                    $enteredAtStr,
                    $item['estimation_date_formatted'] ?? '-',
                    $timeDiff,
                    $statusLabel
                ];
            }
        }

        // Blank separator
        $rows[] = [''];
        // Generated meta
        $rows[] = ['Laporan di-generate pada:', date('d/m/Y H:i')];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A4:C4');
        $sheet->mergeCells('A10:G10');

        $totalRows = $sheet->getHighestRow();

        // Set horizontal alignment
        $sheet->getStyle('B6:B8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D12:G' . ($totalRows - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A11:G11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e293b']],
            ],
            2 => [
                'font' => ['italic' => true, 'color' => ['rgb' => '475569']],
            ],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85'] // Brand Green
                ]
            ],
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '475569'] // Dark Slate
                ]
            ],
            10 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85']
                ]
            ],
            11 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '475569']
                ]
            ],
            ($totalRows) => [
                'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '94a3b8']],
            ]
        ];
    }
}
