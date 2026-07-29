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

        $dealNominal = $this->upsell['oto_deal_nominal'] ?? 0;
        $dealVolume = $this->upsell['oto_deal_volume'] ?? 0;
        $arpuDeal = $this->upsell['arpu_oto_deal'] ?? 0;

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
            // Row 6 - 8: Metrics (Deal)
            ['Total OTO DEAL (Accepted)', 'Rp ' . number_format($dealNominal, 0, ',', '.'), 'Total nominal penawaran OTO yang disetujui (ACCEPTED)'],
            ['Volume SPK DEAL', $dealVolume . ' SPK', 'Jumlah SPK yang deal'],
            ['ARPU DEAL', 'Rp ' . number_format($arpuDeal, 0, ',', '.'), 'Rata-rata nominal deal per SPK'],
            // Row 9 - 11: Metrics (Prospect)
            ['Total OTO PROSPECT (All)', 'Rp ' . number_format($totalNominal, 0, ',', '.'), 'Total nominal estimasi dari seluruh prospek OTO'],
            ['Volume SPK PROSPECT', $totalVolume . ' SPK', 'Jumlah seluruh SPK prospek'],
            ['ARPU PROSPECT', 'Rp ' . number_format($arpu, 0, ',', '.'), 'Rata-rata nominal prospek per SPK'],
            
            // Row 12: Blank separator
            [''],
            
            // Row 13: Section 2 Header
            ['RINCIAN DATA TRANSAKSI ONE-TIME OFFER (OTO)'],
            // Row 14: Table Headers
            ['Layanan OTO & Status', 'No. SPK', 'Jumlah', 'Total Nominal']
        ];

        // Row 15+: Items
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
        $sheet->mergeCells('A13:D13');

        $totalRows = $sheet->getHighestRow();

        // Set horizontal alignment
        $sheet->getStyle('B6:B11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C15:D' . ($totalRows - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A14:D14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

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
            13 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85']
                ]
            ],
            14 => [
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
