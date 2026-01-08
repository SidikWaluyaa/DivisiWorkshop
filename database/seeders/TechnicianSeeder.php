<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TechnicianSeeder extends Seeder
{
    public function run()
    {
        $technicians = [
            'Suhali', 'Ojek', 'Rian', 'Jajat', 'Jerry',
            'Devi', 'Ayi', 'Asep', 'Fikri', 'Acep', 'Elin', 'Hadi', 'Aji', 'Dedi',
            'Yayan', 'Herman', 'Rizal', 'Dadang',
            'Ferry', 'UU', 'Edi', 'Agus', 'Dede', 'Dadan', 'Iyan', 'Dede 2', 'Jujun', 'Toni', 'Ade Rahmat', 'Gugum', 'Haris',
            'Padon'
        ];

        // Unique names only
        $technicians = array_unique($technicians);
        
        foreach ($technicians as $name) {
            // Check if exists to avoid duplicates
            if (!User::where('name', $name)->exists()) {
                User::create([
                    'name' => $name,
                    'email' => Str::slug($name) . '@workshop.com', // Dummy email
                    'password' => Hash::make('12345678'),
                    'role' => 'technician',
                ]);
            }
        }
    }
}
