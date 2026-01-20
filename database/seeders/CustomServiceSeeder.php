<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the generic service already exists by name
        $exists = DB::table('services')->where('name', 'Custom Service')->exists();

        if (!$exists) {
            DB::table('services')->insert([
                'name' => 'Custom Service',
                'category' => 'Custom',
                'price' => 0, // Price will be set dynamically via pivot 'cost'
                'unit' => 'pcs',
                'description' => 'Placeholder for custom manual services',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Custom Service placeholder created successfully.');
        } else {
            $this->command->info('Custom Service placeholder already exists.');
        }
    }
}
