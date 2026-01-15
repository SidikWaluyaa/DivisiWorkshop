<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin / Owner (Access All)
        User::updateOrCreate(
            ['email' => 'admin@workshop.com'],
            [
                'name' => 'Admin Gudang',
                'password' => Hash::make('password'),
                'role' => 'admin', // Fixed role to admin
            ]
        );

        // 2. Washer (Station: Cuci)
        User::updateOrCreate(
            ['email' => 'washer@workshop.com'],
            [
                'name' => 'Staff Cuci',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        // 3. Technician (Station: Assessment, Prep, Production)
        User::updateOrCreate(
            ['email' => 'tech@workshop.com'],
            [
                'name' => 'Dr. Shoe (Tech)',
                'password' => Hash::make('password'),
                'role' => 'technician', // Adjusting role based on context
            ]
        );

        // 4. QC Officer (Station: QC)
        User::updateOrCreate(
            ['email' => 'qc@workshop.com'],
            [
                'name' => 'Inspektur QC',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        // 5. Front Desk (Station: Reception & Finish)
        User::updateOrCreate(
            ['email' => 'kasir@workshop.com'],
            [
                'name' => 'Kasir Finish',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );
    }
}
