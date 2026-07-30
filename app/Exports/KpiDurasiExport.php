<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KpiDurasiExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $startLabel;
    protected $endLabel;

    public function __construct(array $data, string $startLabel, string $endLabel)
    {
        $this->data = $data;
        $this->startLabel = $startLabel;
        $this->endLabel = $endLabel;
    }

    public function array(): array
    {
        $rows = [
            // Row 1: Title
            ['SHOE WORKSHOP - LAPORAN ANALISIS DURASI TAHAPAN (KPI)'],
            // Row 2: Date Info
            ['Periode SPK:', $this->startLabel . ' s/d ' . $this->endLabel],
            // Row 3: Blank separator
            [''],
            
            // Row 4: Table Headers
            [
                'No. SPK',
                'Nama Pelanggan',
                'Status Saat Ini',
                'Masuk PREP',
                'Keluar PREP',
                'Durasi PREP',
                'Masuk SORTIR',
                'Keluar SORTIR',
                'Durasi SORTIR',
                'Masuk PROD',
                'Keluar PROD',
                'Durasi PROD',
                'Masuk QC',
                'Keluar QC',
                'Durasi QC'
            ]
        ];

        // Row 5+: Data
        if (empty($this->data)) {
            $rows[] = ['Tidak ada data SPK pada periode ini.', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        } else {
            foreach ($this->data as $item) {
                $rows[] = [
                    $item['spk_number'],
                    $item['customer_name'],
                    $item['current_status'],
                    $item['prep_enter'],
                    $item['prep_exit'],
                    $item['prep_duration'],
                    $item['sortir_enter'],
                    $item['sortir_exit'],
                    $item['sortir_duration'],
                    $item['prod_enter'],
                    $item['prod_exit'],
                    $item['prod_duration'],
                    $item['qc_enter'],
                    $item['qc_exit'],
                    $item['qc_duration']
                ];
            }
        }

        // Blank separator
        $rows[] = [''];
        // Meta generated at
        $rows[] = ['Laporan di-generate pada:', date('d/m/Y H:i')];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:O1');
        $totalRows = $sheet->getHighestRow();

        // Alignment formatting
        $sheet->getStyle('A4:O4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:O4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // Align center for timestamps and durations (columns C to O)
        $sheet->getStyle('C5:O' . ($totalRows - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e293b']],
            ],
            2 => [
                'font' => ['italic' => true, 'color' => ['rgb' => '475569']],
            ],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 10],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85'] // Brand Green
                ]
            ],
            ($totalRows) => [
                'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '94a3b8']],
            ]
        ];
    }
}
