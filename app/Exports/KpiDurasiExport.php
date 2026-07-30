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
            ['SHOE WORKSHOP - LAPORAN RINGKASAN KPI DURASI TAHAPAN SPK'],
            // Row 2: Date Info
            ['Periode Analisis:', $this->startLabel . ' s/d ' . $this->endLabel],
            // Row 3: Blank separator
            [''],
            
            // Row 4: Table Headers
            [
                'Stasiun / Tahapan',
                'Masuk (Kotor)',
                'Masuk dari CX (Anomali)',
                'Masuk (Bersih)',
                'Keluar (Kotor)',
                'Keluar ke CX (Anomali)',
                'Keluar (Bersih)'
            ]
        ];

        // Row 5+: Data
        foreach ($this->data as $item) {
            $rows[] = [
                $item['stage'],
                $item['masuk_kotor'],
                $item['masuk_cx'],
                $item['masuk_bersih'],
                $item['keluar_kotor'],
                $item['keluar_cx'],
                $item['keluar_bersih']
            ];
        }

        // Blank separator
        $rows[] = [''];
        // Meta generated at
        $rows[] = ['Laporan di-generate pada:', date('d/m/Y H:i')];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:G1');
        $totalRows = $sheet->getHighestRow();

        // Alignment formatting
        $sheet->getStyle('A4:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:G4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        $sheet->getStyle('B5:G' . ($totalRows - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
            ($totalRows) => [
                'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '94a3b8']],
            ]
        ];
    }
}
