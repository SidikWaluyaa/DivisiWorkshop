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
        if (empty($row['name'])) {
            return null;
        }

        $picUserId = null;
        if (!empty($row['pic'])) {
            $picSearch = trim($row['pic']);
            $user = \App\Models\User::where('email', $picSearch)
                ->orWhere('name', 'like', "%{$picSearch}%")
                ->orWhere('id', $picSearch)
                ->first();
            if ($user) {
                $picUserId = $user->id;
            }
        }

        $category = null;
        if (!empty($row['category'])) {
            $catNorm = strtoupper(trim($row['category']));
            if (in_array($catNorm, ['PRODUCTION', 'SHOPPING'])) {
                $category = $catNorm;
            }
        }

        return Material::updateOrCreate(
            [
                'name' => $row['name'],
                'type' => $row['type'] ?? 'Material Upper',
            ],
            [
                'category' => $category,
                'sub_category' => $row['sub_category'] ?? null,
                'size' => $row['size'] ?? null,
                'stock' => $row['stock'] ?? 0,
                'unit' => $row['unit'] ?? 'pcs',
                'price' => $row['price'] ?? 0,
                'min_stock' => $row['min_stock'] ?? 5,
                'status' => $row['status'] ?? 'Ready',
                'pic_user_id' => $picUserId,
            ]
        );
    }
}
