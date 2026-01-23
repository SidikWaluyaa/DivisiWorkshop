<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StorageRack;

class StorageRackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $racks = [
            // Area A - Lantai 1 (Capacity: 20)
            ['rack_code' => 'A1', 'location' => 'Lantai 1 - Area A', 'capacity' => 20],
            ['rack_code' => 'A2', 'location' => 'Lantai 1 - Area A', 'capacity' => 20],
            ['rack_code' => 'A3', 'location' => 'Lantai 1 - Area A', 'capacity' => 20],
            ['rack_code' => 'A4', 'location' => 'Lantai 1 - Area A', 'capacity' => 20],
            ['rack_code' => 'A5', 'location' => 'Lantai 1 - Area A', 'capacity' => 20],

            // Area B - Lantai 1 (Capacity: 20)
            ['rack_code' => 'B1', 'location' => 'Lantai 1 - Area B', 'capacity' => 20],
            ['rack_code' => 'B2', 'location' => 'Lantai 1 - Area B', 'capacity' => 20],
            ['rack_code' => 'B3', 'location' => 'Lantai 1 - Area B', 'capacity' => 20],
            ['rack_code' => 'B4', 'location' => 'Lantai 1 - Area B', 'capacity' => 20],
            ['rack_code' => 'B5', 'location' => 'Lantai 1 - Area B', 'capacity' => 20],

            // Area C - Lantai 2 (Capacity: 15)
            ['rack_code' => 'C1', 'location' => 'Lantai 2 - Area C', 'capacity' => 15],
            ['rack_code' => 'C2', 'location' => 'Lantai 2 - Area C', 'capacity' => 15],
            ['rack_code' => 'C3', 'location' => 'Lantai 2 - Area C', 'capacity' => 15],
            ['rack_code' => 'C4', 'location' => 'Lantai 2 - Area C', 'capacity' => 15],
            ['rack_code' => 'C5', 'location' => 'Lantai 2 - Area C', 'capacity' => 15],

            // Area D - Lantai 2 (Capacity: 15)
            ['rack_code' => 'D1', 'location' => 'Lantai 2 - Area D', 'capacity' => 15],
            ['rack_code' => 'D2', 'location' => 'Lantai 2 - Area D', 'capacity' => 15],
            ['rack_code' => 'D3', 'location' => 'Lantai 2 - Area D', 'capacity' => 15],
            ['rack_code' => 'D4', 'location' => 'Lantai 2 - Area D', 'capacity' => 15],
            ['rack_code' => 'D5', 'location' => 'Lantai 2 - Area D', 'capacity' => 15],
        ];

        foreach ($racks as $rack) {
            StorageRack::create([
                'rack_code' => $rack['rack_code'],
                'location' => $rack['location'],
                'capacity' => $rack['capacity'],
                'current_count' => 0,
                'status' => 'active',
                'notes' => null,
            ]);
        }

        $this->command->info('Storage racks seeded successfully!');
        $this->command->info('Total racks created: ' . count($racks));
        $this->command->info('Total capacity: ' . array_sum(array_column($racks, 'capacity')) . ' items');
    }
}
