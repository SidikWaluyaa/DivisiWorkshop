<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['name' => 'Angelus Paint Black', 'unit' => 'bottle', 'stock' => 10],
            ['name' => 'Angelus Paint White', 'unit' => 'bottle', 'stock' => 5],
            ['name' => 'Angelus Paint Red', 'unit' => 'bottle', 'stock' => 0], // Empty stock test
            ['name' => 'Lem Kuning (Fox)', 'unit' => 'tin', 'stock' => 20],
            ['name' => 'Lem Putih (Graft)', 'unit' => 'tin', 'stock' => 15],
            ['name' => 'Benang Jahit Sol', 'unit' => 'roll', 'stock' => 50],
            ['name' => 'Vibram Sole 42', 'unit' => 'pair', 'stock' => 2],
        ];

        foreach ($materials as $m) {
            Material::create($m);
        }
    }
}
