<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BankMutationTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'transaction_date',
            'description',
            'amount',
            'mutation_type',
            'bank_code',
            'invoice_number',
        ];
    }

    public function array(): array
    {
        // Example rows to guide the user
        return [
            ['2026-03-07', 'Pembayaran DP sepatu Nike', 150000, 'CR', 'BCA', 'INV-260305-95DC'],
            ['2026-03-07', 'Pelunasan reparasi', 350000, 'CR', 'MANDIRI', 'INV-260301-A1B2'],
            ['2026-03-06', 'Biaya admin bank', 50000, 'DB', 'BCA', ''],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1B8A68'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 22,
            'C' => 15,
            'D' => 35,
            'E' => 12,
            'F' => 15,
        ];
    }
}
