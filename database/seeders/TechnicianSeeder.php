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
        
        $specializations = [
            'Washing', 'Sol Repair', 'Upper Repair', 'Repaint', 'Treatment', // Technical
            'Jahit', 'Clean Up', 'PIC QC', // QC
            'PIC Material Sol', 'PIC Material Upper' // Material
        ];

        foreach ($technicians as $index => $name) {
            // Cycle through specializations
            $spec = $specializations[$index % count($specializations)];
            
            User::updateOrCreate(
                ['name' => $name], // Search by name
                [
                    'email' => Str::slug($name) . '@workshop.com', // Dummy email
                    'password' => Hash::make('12345678'),
                    'role' => 'technician',
                    'specialization' => $spec,
                ]
            );
        }
    }
}
