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
        User::create([
            'name' => 'Admin Gudang',
            'email' => 'admin@workshop.com',
            'password' => Hash::make('password'), // password
        ]);

        // 2. Washer (Station: Cuci)
        User::create([
            'name' => 'Staff Cuci',
            'email' => 'washer@workshop.com',
            'password' => Hash::make('password'),
        ]);

        // 3. Technician (Station: Assessment, Prep, Production)
        User::create([
            'name' => 'Dr. Shoe (Tech)',
            'email' => 'tech@workshop.com',
            'password' => Hash::make('password'),
        ]);

        // 4. QC Officer (Station: QC)
        User::create([
            'name' => 'Inspektur QC',
            'email' => 'qc@workshop.com',
            'password' => Hash::make('password'),
        ]);

        // 5. Front Desk (Station: Reception & Finish)
        User::create([
            'name' => 'Kasir Finish',
            'email' => 'kasir@workshop.com',
            'password' => Hash::make('password'),
        ]);
    }
}
