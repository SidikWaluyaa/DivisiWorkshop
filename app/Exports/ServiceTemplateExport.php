<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ServiceTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Deep Clean', 'Cleaning', '50000', '30', 'Cuci bersih luar dalam'],
            ['Reglue Full', 'Repair', '150000', '60', 'Lem ulang seluruh bagian'],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'category',
            'price',
            'duration_minutes',
            'description',
        ];
    }
}
