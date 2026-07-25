<?php

namespace App\Imports;

use App\Models\Material;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\ValidationException;

class MaterialsImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $errors = [];
        $seenInExcel = [];

        // 1. Pre-validate all rows
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // Row number in Excel (1-based index + 1 for header row)

            // Skip completely empty rows
            if (empty($row['name'])) {
                continue;
            }

            $name = trim($row['name']);
            $type = $row['type'] ?? 'Material Upper';
            $size = isset($row['size']) ? trim($row['size']) : null;
            $unit = $row['unit'] ?? 'pcs';

            // Key to check uniqueness of the combination
            $key = strtolower("{$name}|{$type}|{$size}|{$unit}");

            // A. Check for duplicates within the uploaded Excel itself
            if (isset($seenInExcel[$key])) {
                $errors["excel_{$index}"] = "Baris {$rowNumber}: Duplikat di Excel dengan Baris " . $seenInExcel[$key] . " (Nama: {$name}, Ukuran: " . ($size ?: '-') . ")";
            } else {
                $seenInExcel[$key] = $rowNumber;
            }

            // B. Check if it already exists in database (by ID if provided, or by combination)
            $existsInDb = false;
            $id = $row['id'] ?? null;
            if ($id) {
                $existsInDb = Material::where('id', $id)->exists();
            } else {
                $existsInDb = Material::where('name', $name)
                    ->where('type', $type)
                    ->where('unit', $unit)
                    ->where(function ($q) use ($size) {
                        if (!empty($size)) {
                            $q->where('size', $size);
                        } else {
                            $q->whereNull('size')->orWhere('size', '');
                        }
                    })
                    ->exists();
            }

            if ($existsInDb) {
                $errors["db_{$index}"] = "Baris {$rowNumber}: Material sudah terdaftar di database (Nama: {$name}, Ukuran: " . ($size ?: '-') . ")";
            }
        }

        // 2. If there are any validation errors, abort the entire process
        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        // 3. If everything is unique and clean, proceed to insert all rows
        foreach ($rows as $row) {
            if (empty($row['name'])) {
                continue;
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

            Material::create([
                'name' => trim($row['name']),
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
        }
    }
}
