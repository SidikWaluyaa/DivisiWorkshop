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

        $picSearch = isset($row['pic']) ? trim($row['pic']) : (isset($row['pic_name']) ? trim($row['pic_name']) : null);
        $picUserId = null;
        if (!empty($picSearch)) {
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

        // Support both template ('price') and export ('price_rp') headers
        $price = $row['price'] ?? $row['price_rp'] ?? 0;
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

        // If ID is provided, try updating by ID first (useful for re-importing exports)
        $id = $row['id'] ?? null;
        if ($id) {
            $material = Material::find($id);
            if ($material) {
                $material->update([
                    'name' => $row['name'],
                    'type' => $row['type'] ?? 'Material Upper',
                    'size' => $size,
                    'category' => $category,
                    'sub_category' => $row['sub_category'] ?? null,
                    'stock' => $row['stock'] ?? 0,
                    'unit' => $row['unit'] ?? 'pcs',
                    'price' => $price,
                    'min_stock' => $row['min_stock'] ?? 5,
                    'status' => $row['status'] ?? 'Ready',
                    'pic_user_id' => $picUserId,
                ]);
                return $material;
            }
        }

        // Fallback to name, type, size matching
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
