<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersTemplateExport implements WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'SPK',
            'Customer',
            'No WA',
            'Alamat',
            'Brand',
            'Size',
            'Warna',
            'Tanggal Masuk',
            'Estimasi Selesai',
            'Prioritas'
        ];
    }

    public function title(): string
    {
        return 'Template Import Order';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
