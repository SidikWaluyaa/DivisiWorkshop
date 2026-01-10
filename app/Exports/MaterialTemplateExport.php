<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MaterialTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        return [
            ['Vibram XS Grip', 'Material Sol', 'Vibram', '40', '10', 'pcs', '350000', '5', 'Ready'],
            ['Kulit Sapi Premium', 'Material Upper', '', '', '5', 'lembar', '1500000', '2', 'Belanja'],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'type',
            'sub_category',
            'size',
            'stock',
            'unit',
            'price',
            'min_stock',
            'status',
        ];
    }
}
