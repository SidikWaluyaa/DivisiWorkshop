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

        $price = $row['price'] ?? 0;
        if (is_string($price)) {
            // Bersihkan format Rp dan spasi
            $priceCleaned = preg_replace('/[Rr]p|\s+/', '', $price);
            
            // Format Indonesia: titik sebagai ribuan (cth: 12.000) -> hapus titik
            if (strpos($priceCleaned, '.') !== false && strpos($priceCleaned, ',') === false) {
                if (preg_match('/^\d+(\.\d{3})+$/', $priceCleaned)) {
                    $priceCleaned = str_replace('.', '', $priceCleaned);
                }
            } elseif (strpos($priceCleaned, ',') !== false) {
                // Koma desimal (cth: 12.000,50) -> hapus titik, ubah koma ke titik
                $priceCleaned = str_replace('.', '', $priceCleaned);
                $priceCleaned = str_replace(',', '.', $priceCleaned);
            }
            $price = floatval($priceCleaned);
        }

        $size = isset($row['size']) ? trim($row['size']) : null;

        return Material::updateOrCreate(
            [
                'name' => $row['name'],
                'type' => $row['type'] ?? 'Material Upper',
                'size' => $size,
            ],
            [
                'category' => $category,
                'sub_category' => $row['sub_category'] ?? null,
                'stock' => $row['stock'] ?? 0,
                'unit' => $row['unit'] ?? 'pcs',
                'price' => $price,
                'min_stock' => $row['min_stock'] ?? 5,
                'status' => $row['status'] ?? 'Ready',
                'pic_user_id' => $picUserId,
            ]
        );
    }
}
