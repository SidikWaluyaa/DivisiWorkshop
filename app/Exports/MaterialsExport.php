<?php

namespace App\Exports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MaterialsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Material::orderBy('type')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Type',
            'Sub Category',
            'Size',
            'Stock',
            'Unit',
            'Price (Rp)',
            'Min Stock',
            'Status',
            'PIC Name',
            'PIC Phone',
        ];
    }

    public function map($material): array
    {
        return [
            $material->id,
            $material->name,
            $material->type,
            $material->sub_category,
            $material->size,
            $material->stock,
            $material->unit,
            $material->price,
            $material->min_stock,
            $material->status,
            $material->pic ? $material->pic->name : '',
            $material->pic ? $material->pic->phone : '',
        ];
    }
}
