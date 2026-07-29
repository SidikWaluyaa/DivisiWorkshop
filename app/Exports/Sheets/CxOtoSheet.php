<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CxOtoSheet implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    protected $upsell;
    protected $startDate;
    protected $endDate;

    public function __construct($upsell, $startDate, $endDate)
    {
        $this->upsell = $upsell;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'OTO Revenue';
    }

    public function array(): array
    {
        $rangeLabel = $this->startDate->format('d/m/Y') . ' s/d ' . $this->endDate->format('d/m/Y');
        
        $totalNominal = $this->upsell['oto_nominal'] ?? 0;
        $totalVolume = $this->upsell['oto_volume'] ?? 0;
        $arpu = $this->upsell['arpu_oto'] ?? 0;
        $items = $this->upsell['oto_items'] ?? [];

        $rows = [
            // Row 1: Main Header
            ['SHOE WORKSHOP - LAPORAN REVENUE ONE-TIME OFFER (OTO)'],
            // Row 2: Filter Info
            ['Periode Laporan:', $rangeLabel],
            // Row 3: Blank separator
            [''],
            
            // Row 4: Section 1 Header
            ['RINGKASAN REVENUE ONE-TIME OFFER (OTO)'],
            // Row 5: Column Headers
            ['Nama Metrik', 'Nilai Metrik', 'Keterangan'],
            // Row 6 - 8: Metrics
            ['Total Nominal Prospect', 'Rp ' . number_format($totalNominal, 0, ',', '.'), 'Total nominal estimasi dari prospek OTO aktif'],
            ['Volume SPK', $totalVolume . ' SPK', 'Total SPK yang mendapatkan penawaran OTO'],
            ['ARPU (Average Revenue Per Unit)', 'Rp ' . number_format($arpu, 0, ',', '.'), 'Rata-rata estimasi penawaran OTO per SPK'],
            
            // Row 9: Blank separator
            [''],
            
            // Row 10: Section 2 Header
            ['RINCIAN DATA TRANSAKSI ONE-TIME OFFER (OTO)'],
            // Row 11: Table Headers
            ['Layanan OTO & Status', 'No. SPK', 'Jumlah', 'Total Nominal']
        ];

        // Row 12+: Items
        if (empty($items)) {
            $rows[] = ['Belum ada OTO yang diterima dalam periode ini.', '', '', ''];
        } else {
            foreach ($items as $item) {
                $rows[] = [
                    $item->title,
                    $item->spk_number,
                    $item->count . ' Transaksi',
                    'Rp ' . number_format($item->total_revenue, 0, ',', '.')
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
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A4:C4');
        $sheet->mergeCells('A10:D10');

        $totalRows = $sheet->getHighestRow();

        // Set horizontal alignment
        $sheet->getStyle('B6:B8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C12:D' . ($totalRows - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A11:D11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

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
