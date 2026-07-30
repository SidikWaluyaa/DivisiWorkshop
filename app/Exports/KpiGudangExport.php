<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KpiGudangExport implements FromArray, ShouldAutoSize, WithStyles
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
            ['SHOE WORKSHOP - LAPORAN RINGKASAN KPI GUDANG'],
            // Row 2: Date Info
            ['Periode Analisis:', $this->startLabel . ' s/d ' . $this->endLabel],
            // Row 3: Blank separator
            [''],
            
            // Row 4: Table Headers
            [
                'Metrik',
                'Jumlah',
                'Keterangan'
            ],
            // Row 5+: Data
            ['1. Sepatu Masuk (Before)', $this->data['sepatu_masuk'] . ' Pasang', 'Diterima fisik di Gudang'],
            ['2. SPK Print (OTW WS)', $this->data['spk_otw'] . ' Pasang', 'Dikirim ke reparasi / manifest'],
            ['3. SPK Tertahan (QC Reject)', $this->data['qc_reject'] . ' Pasang', 'Gagal penerimaan awal'],
            ['4. After Masuk', $this->data['after_masuk'] . ' Pasang', 'Selesai reparasi masuk rak'],
            ['5. Sepatu Keluar', $this->data['sepatu_keluar'] . ' Pasang', 'Pengambilan & kirim lunas'],
        ];

        // Blank separator
        $rows[] = [''];
        // Meta generated at
        $rows[] = ['Laporan di-generate pada:', date('d/m/Y H:i')];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:C1');
        $totalRows = $sheet->getHighestRow();

        // Alignment formatting
        $sheet->getStyle('A4:C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:C4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        $sheet->getStyle('B5:B9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                    'startColor' => ['rgb' => 'd97706'] // Amber-600 to differentiate from Workshop green
                ]
            ],
            ($totalRows) => [
                'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '94a3b8']],
            ]
        ];
    }
}
