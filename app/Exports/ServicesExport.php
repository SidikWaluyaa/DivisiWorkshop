<?php

namespace App\Exports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ServicesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Service::orderBy('category')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Category',
            'Price',
            'Duration (Minutes)',
            'Description',
        ];
    }

    public function map($service): array
    {
        return [
            $service->id,
            $service->name,
            $service->category,
            $service->price,
            $service->duration_minutes,
            $service->description,
        ];
    }
}
