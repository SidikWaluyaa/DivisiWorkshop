<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WarehouseAnalyticsExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $stats;
    protected $startDate;
    protected $endDate;

    public function __construct($stats, $startDate, $endDate)
    {
        $this->stats = $stats;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $rangeLabel = $this->startDate->format('d/m/Y') . ' s/d ' . $this->endDate->format('d/m/Y');

        return [
            // Row 1: Main Header
            ['SHOE WORKSHOP - LAPORAN KINERJA GUDANG (WAREHOUSE)'],
            // Row 2: Filter Info
            ['Periode Laporan:', $rangeLabel],
            // Row 3: Blank separator
            [''],
            
            // Row 4: Section Header
            ['RINGKASAN METRIK KINERJA GUDANG'],
            // Row 5: Column Headers
            ['Nama Metrik', 'Nilai Metrik', 'Keterangan'],
            // Row 6 - 13: Hero Metrics (excluding qc_reject)
            ['1. Sepatu Masuk (Before)', $this->stats['incoming_day'] . ' Pasang', 'Diterima Fisik di Gudang'],
            ['2. SPK Print (Otw Ws)', $this->stats['spk_print'] . ' Pasang', 'Dikirim ke Reparasi / Manifest'],
            ['3. After Masuk', $this->stats['after_masuk'] . ' Pasang', 'Selesai Reparasi Masuk Rak'],
            ['4. Sepatu Keluar', $this->stats['sepatu_keluar'] . ' Pasang', 'Pengambilan & Kirim Lunas'],
            ['5. Rak Inbound (Transit)', $this->stats['inbound_inventory'] . ' Pasang', 'Fisik di Rak Penerimaan / Sebelum'],
            ['6. Rak Finish (Selesai)', $this->stats['finish_inventory'] . ' Pasang', 'Fisik di Rak Selesai / Siap Ambil'],
            ['7. Clearance Rate Inbound', $this->stats['clearance_rate_before'] . '%', 'Inbound Flow Balance'],
            ['8. Clearance Rate Outbound', $this->stats['clearance_rate_after'] . '%', 'Outbound Flow Balance'],
            
            // Row 14: Blank separator
            [''],
            // Row 15: Generated meta
            ['Laporan di-generate pada:', date('d/m/Y H:i')]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for headers
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A4:C4');

        // Set horizontal alignment
        $sheet->getStyle('B6:B13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return [
            // Row 1: Title styling
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e293b']],
            ],
            // Row 2: Metadata styling
            2 => [
                'font' => ['italic' => true, 'color' => ['rgb' => '475569']],
            ],
            // Row 4: Section Header styling
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85'] // Brand Green
                ]
            ],
            // Row 5: Heading Columns styling
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '475569'] // Dark Slate
                ]
            ],
            // Row 15: Footer info styling
            15 => [
                'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '94a3b8']],
            ]
        ];
    }
}
