<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            'Climac Putih Hitam', 'Climac Putih Gum', 'Climac Putih Orange', 'Climac Putih Coklat', 'Climac Putih Navy',
            'Climac Abu Navy', 'Climac Hitam Polos', 'Milano Hitam Putih', 'Milano Transparan Putih', 'Nike Dunk Coklat',
            'Cakra Hitam', 'Sergab Hitam', 'Converse Burgundy', 'Converse Hitam', 'Waffle Gum', 'Waflle Hitam',
            'Converse Strip Hitam', 'Converse Putih Polos', 'Converse Putih Gading Polos', 'Converse Hitam Polos',
            'Converse Gum Polos', 'Vans Putih Strip Hitam', 'Vans Putih Polos', 'Vans Gum Polos', 'Vans Gum Strip Hitam',
            'Vans Hitam Polos', 'Bemper Vans Putih', 'Bemper Vans Gum', 'Bemper Vans Hitam', 'Bemper Converse Putih',
            'Bemper Converse Hitam', 'Toucap Converse Hitam', 'Bemper Jackparcel Gum', 'Bemper Jackparcel Hitam',
            'Bemper Jackparcel Putih', 'Puma Putih', 'Puma Hitam', 'Puma Orange', 'Samba Sw Gum', 'Samba Sw Dark Grey',
            'Pogba Hitam', 'Lubita Beige', 'Bluemoon', 'Komando Hitam', 'Komando Gum', 'Spike x ASJ Hitam Putih',
            'Panji', 'Granica Hitam', 'Granica Ivory', 'Nike F1 Merah Putih',
            // Bola Series
            'Bola 1', 'Bola 2', 'Bola 3', 'Bola 4', 'Bola 5', 'Bola 6', 'Bola 7', 'Bola 8', 'Bola 9', 'Bola 10',
            'Bola 11', 'Bola 12', 'Bola 13', 'Bola 14', 'Bola 15', 'Bola 16', 'Bola 17', 'Bola 18', 'Bola 19', 'Bola 20',
            'Bola 21', 'Bola 22', 'Bola 23', 'Bola 24', 'Bola 25', 'Bola 26', 'Bola 27', 'Bola 28', 'Bola 29', 'Bola 30',
            'Bola 31', 'Bola 32', 'Bola 33',
            // Others
            'Ripple Coklat', 'Ripple Hitam', 'Ripple Putih',
            'Sol Potong LNP Hitam', 'Sol Potong LNP Gum', 'Sol Potong LNP Putih',
            'Rander Hitam Putih', 'Rander Rainbow', 'Hak Golfnite', 'Sol Golfnite',
            'Bemper Converse Putih Gading', 'Bemper Vans Putih Gading',
            'Toecap Converse Putih', 'Toecap Converse Putih Gading',
            'Toecap Vans Putih', 'Toecap Vans Hitam', 'Toecap Vans Putih Gading',
            'Alas LNP Hijau', 'Alas LNP Biru', 'Alas LNP Orange', 'Alas LNP Kuning',
            'Alas LNP Hitam', 'Alas LNP Gum', 'Alas LNP Putih', 'Alas LNP Hitam B', 'Alas LNP Gum B',
            'Alas CTR', 'Alas Onitsuka Cream', 'Alas Komando Hunter Hitam',
            // Abstrak Series
            'Abstrak Navy', 'Abstrak Orange', 'Abstrak Pink', 'Abstrak Polos', 'Abstrak Ungu',
            'Eva Putih'
        ];

        $upperMaterials = [
            'Lem Kuning (Fox)', 'Lem Putih (PU)', 'Primer Sepatu', 'Aseton / Pembersih', 'Hardener',
            'Cat Leather Hitam', 'Cat Leather Putih', 'Cat Leather Coklat', 
            'Cat Canvas Merah', 'Cat Canvas Biru', 'Cat Canvas Kuning', 'Cat Canvas Hitam',
            'Suede Cleaner', 'Leather Filler', 'Leather Conditioner',
            'Benang Jahit Hitam', 'Benang Jahit Putih', 'Benang Jahit Coklat', 'Benang Jahit Navy',
            'Insole Busa 3mm', 'Insole Busa 5mm', 'Kain Mesh Hitam', 'Kain Mesh Putih',
            'Kulit Sintetis Hitam', 'Kulit Sintetis Coklat', 'Suede Patch Hitam'
        ];

        // Seed Sol Materials
        foreach ($materials as $name) {
            // Determine Category & Type
            $type = 'Material Sol'; 
            $sub = 'Sol Jadi'; 

            $lowerName = strtolower($name);

            if (str_contains($lowerName, 'sol potong') || str_contains($lowerName, 'alas lnp') || str_contains($lowerName, 'eva ')) {
                $sub = 'Sol Potong';
            } elseif (str_contains($lowerName, 'vibram')) {
                $sub = 'Vibram';
            } elseif (str_contains($lowerName, 'foxing') || str_contains($lowerName, 'bemper') || str_contains($lowerName, 'toecap') || str_contains($lowerName, 'toucap')) {
                $sub = 'Foxing';
            } else {
                $sub = 'Sol Jadi';
            }

            // Random Size for Sol
            $size = null;
            if ($type == 'Material Sol') {
                $sizes = ['39', '40', '41', '42', '43', '44', 'S', 'M', 'L'];
                $size = $sizes[array_rand($sizes)];
            }

            // Create or Update to force categorization fix
            Material::updateOrCreate(
                ['name' => $name],
                [
                    'type' => $type,
                    'sub_category' => $sub,
                    'size' => $size,
                    'stock' => 10,
                    'unit' => 'pasang',
                    'price' => rand(25000, 150000), // Dummy price for Sol (25k - 150k)
                    'min_stock' => 5,
                    'status' => 'Ready'
                ]
            );
        }

        // Seed Upper Materials
        foreach ($upperMaterials as $name) {
            Material::updateOrCreate(
                ['name' => $name],
                [
                    'type' => 'Material Upper',
                    'sub_category' => null,
                    'stock' => 20,
                    'unit' => str_contains(strtolower($name), 'lem') || str_contains(strtolower($name), 'cat') ? 'kaleng/botol' : (str_contains(strtolower($name), 'benang') ? 'roll' : 'pcs'),
                    'price' => rand(15000, 75000), // Dummy price for Upper (15k - 75k)
                    'min_stock' => 5,
                    'status' => 'Ready'
                ]
            );
        }
    }
}
