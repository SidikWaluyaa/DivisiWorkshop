<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['name' => 'Angelus Paint Black', 'unit' => 'bottle', 'stock' => 10, 'category' => 'Material Upper', 'price' => 50000, 'min_stock' => 5],
            ['name' => 'Angelus Paint White', 'unit' => 'bottle', 'stock' => 5, 'category' => 'Material Upper', 'price' => 50000, 'min_stock' => 5],
            ['name' => 'Angelus Paint Red', 'unit' => 'bottle', 'stock' => 0, 'category' => 'Material Upper', 'price' => 50000, 'min_stock' => 5],
            ['name' => 'Lem Kuning (Fox)', 'unit' => 'tin', 'stock' => 20, 'category' => 'Material Sol', 'price' => 50000, 'min_stock' => 5],
            ['name' => 'Lem Putih (Graft)', 'unit' => 'tin', 'stock' => 15, 'category' => 'Material Sol', 'price' => 50000, 'min_stock' => 5],
            ['name' => 'Benang Jahit Sol', 'unit' => 'roll', 'stock' => 50, 'category' => 'Material Sol', 'price' => 50000, 'min_stock' => 5],
            ['name' => 'Vibram Sole 42', 'unit' => 'pair', 'stock' => 2, 'category' => 'Material Sol', 'price' => 50000, 'min_stock' => 5],
            ['name' => 'Kain Lap Microfiber', 'unit' => 'pcs', 'stock' => 100, 'category' => 'Umum', 'price' => 50000, 'min_stock' => 5],
        ];

        foreach ($materials as $m) {
            Material::updateOrCreate(
                ['name' => $m['name']], // Search by name
                $m // Update/Create with these values
            );
        }
    }
}
