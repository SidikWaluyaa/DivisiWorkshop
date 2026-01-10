<?php

namespace App\Imports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MaterialsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip if name is missing
        if (!isset($row['name'])) {
            return null;
        }

        return Material::updateOrCreate(
            [
                'name' => $row['name'],
                'type' => $row['type'] ?? 'Material Upper',
            ],
            [
                'sub_category' => $row['sub_category'] ?? null,
                'stock' => $row['stock'] ?? 0,
                'unit' => $row['unit'] ?? 'pcs',
                'price' => $row['price'] ?? 0,
                'min_stock' => $row['min_stock'] ?? 5,
                'status' => $row['status'] ?? 'Ready',
            ]
        );
    }
}
