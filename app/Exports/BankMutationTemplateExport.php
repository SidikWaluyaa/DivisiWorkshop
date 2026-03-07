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
            'invoice_number',
            'amount',
            'description',
            'bank_code',
            'mutation_type',
        ];
    }

    public function array(): array
    {
        // Example rows to guide the user
        return [
            ['2026-03-07', 'INV-260305-95DC', 150000, 'Pembayaran DP sepatu Nike', 'BCA', 'CR'],
            ['2026-03-07', 'INV-260301-A1B2', 350000, 'Pelunasan reparasi', 'MANDIRI', 'CR'],
            ['2026-03-06', '', 50000, 'Biaya admin bank', 'BCA', 'DB'],
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
