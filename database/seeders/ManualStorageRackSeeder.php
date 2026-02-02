<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StorageRack;
use App\Enums\StorageCategory;

class ManualStorageRackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $racks = [
            // Rak Manual - M Area (Capacity: 30)
            ['rack_code' => 'M1', 'location' => 'Gudang Manual - Area M', 'capacity' => 30],
            ['rack_code' => 'M2', 'location' => 'Gudang Manual - Area M', 'capacity' => 30],
            ['rack_code' => 'M3', 'location' => 'Gudang Manual - Area M', 'capacity' => 30],
            ['rack_code' => 'M4', 'location' => 'Gudang Manual - Area M', 'capacity' => 30],
            ['rack_code' => 'M5', 'location' => 'Gudang Manual - Area M', 'capacity' => 30],
        ];

        foreach ($racks as $rack) {
            StorageRack::create([
                'rack_code' => $rack['rack_code'],
                'location' => $rack['location'],
                'capacity' => $rack['capacity'],
                'current_count' => 0,
                'status' => 'active',
                'category' => StorageCategory::MANUAL,
                'notes' => 'Rak khusus barang manual/titipan',
            ]);
        }

        $this->command->info('Manual storage racks (M1-M5) seeded successfully!');
    }
}
