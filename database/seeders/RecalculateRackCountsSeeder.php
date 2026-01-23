<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StorageRack;
use App\Models\StorageAssignment;

class RecalculateRackCountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $racks = StorageRack::all();
        $totalFixed = 0;

        foreach ($racks as $rack) {
            $actualCount = StorageAssignment::where('rack_code', $rack->rack_code)
                ->where('status', 'stored')
                ->count();

            if ($rack->current_count !== $actualCount) {
                $this->command->info("Fixing rack {$rack->rack_code}: {$rack->current_count} -> {$actualCount}");
                $rack->update(['current_count' => $actualCount]);
                $totalFixed++;
            }
        }

        $this->command->info("Recalculation complete. Fixed {$totalFixed} racks.");
    }
}
