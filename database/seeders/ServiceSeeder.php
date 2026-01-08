<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // REPARASI SOL
            ['name' => 'Sol-jadi/Cupsole', 'category' => 'Reparasi Sol', 'price' => 250000],
            ['name' => 'Sol-Vibram', 'category' => 'Reparasi Sol', 'price' => 1050000],
            ['name' => 'Ganti Outsole Reguler', 'category' => 'Reparasi Sol', 'price' => 250000],
            ['name' => 'Ganti Outsole Sepatu Anak', 'category' => 'Reparasi Sol', 'price' => 125000],
            ['name' => 'Alas Hak Sepatu Pria', 'category' => 'Reparasi Sol', 'price' => 175000],
            ['name' => 'Alas Hak Sepatu Wanita', 'category' => 'Reparasi Sol', 'price' => 75000],
            ['name' => 'Foxing', 'category' => 'Reparasi Sol', 'price' => 225000],
            ['name' => 'Sol Kulit', 'category' => 'Reparasi Sol', 'price' => 525000],
            ['name' => 'Sol Kulit Goodyear Welt', 'category' => 'Reparasi Sol', 'price' => 1000000],
            ['name' => 'Ganti Sol Crepe', 'category' => 'Reparasi Sol', 'price' => 325000],

            // REPARASI UPPER
            ['name' => 'Ganti Upper Pola Kecil', 'category' => 'Reparasi Upper', 'price' => 75000],
            ['name' => 'Ganti Upper Pola Standard', 'category' => 'Reparasi Upper', 'price' => 175000],
            ['name' => 'Ganti Upper Pola Sedang', 'category' => 'Reparasi Upper', 'price' => 225000],
            ['name' => 'Ganti Upper Pola Besar', 'category' => 'Reparasi Upper', 'price' => 325000],
            ['name' => 'Ganti Upper Customer Upper', 'category' => 'Reparasi Upper', 'price' => 275000],
            ['name' => 'Ganti Lining Mesh', 'category' => 'Reparasi Upper', 'price' => 175000],
            ['name' => 'Ganti Lining Kulit', 'category' => 'Reparasi Upper', 'price' => 225000],
            ['name' => 'Ganti Lining Keseluruhan', 'category' => 'Reparasi Upper', 'price' => 325000],
            ['name' => 'Lapis Kulit', 'category' => 'Reparasi Upper', 'price' => 150000],
            ['name' => 'Buat Insole', 'category' => 'Reparasi Upper', 'price' => 125000],
            ['name' => 'Tambah Busa Insole', 'category' => 'Reparasi Upper', 'price' => 175000],
            ['name' => 'Laser Tulisan/Gambar', 'category' => 'Reparasi Upper', 'price' => 125000],
            ['name' => 'Print DTF', 'category' => 'Reparasi Upper', 'price' => 75000],

            // REPAINT
            ['name' => 'Repaint Standard Color', 'category' => 'Repaint', 'price' => 160000],
            ['name' => 'Repaint Special Color', 'category' => 'Repaint', 'price' => 200000],
            ['name' => 'Repaint Premium Color', 'category' => 'Repaint', 'price' => 375000],
            ['name' => 'Repaint Multicolor', 'category' => 'Repaint', 'price' => 250000],
            ['name' => 'Repaint Patina', 'category' => 'Repaint', 'price' => 250000],
            ['name' => 'Upper Treatment', 'category' => 'Repaint', 'price' => 90000],
            ['name' => 'Unyellowing', 'category' => 'Repaint', 'price' => 125000],
            ['name' => 'Repaint Tas', 'category' => 'Repaint', 'price' => 1150000],

            // MIDSOLE
            ['name' => 'Midsole Model Flat', 'category' => 'Midsole', 'price' => 285000],
            ['name' => 'Midsole Model Non-Flat', 'category' => 'Midsole', 'price' => 335000],
            ['name' => 'Midsole Model Hard/Outdoor', 'category' => 'Midsole', 'price' => 460000],
            ['name' => 'Custom Midsole', 'category' => 'Midsole', 'price' => 525000],
            ['name' => 'Tambahan Midsole (EVA)', 'category' => 'Midsole', 'price' => 125000],
            ['name' => 'Tambahan Midsole (Kulit)', 'category' => 'Midsole', 'price' => 175000],

            // LEM-JAHIT
            ['name' => 'Lem Jahit Reguler', 'category' => 'Lem-Jahit', 'price' => 190000],
            ['name' => 'Lem Jahit Spesial (+ Lapis Kulit)', 'category' => 'Lem-Jahit', 'price' => 275000],
            ['name' => 'Lem Jahit Sepatu Anak', 'category' => 'Lem-Jahit', 'price' => 90000],

            // PAKET
            ['name' => 'Paket Sol Kulit + Alas', 'category' => 'Paket', 'price' => 675000],
            ['name' => 'Paket Midsole Flat + Alas', 'category' => 'Paket', 'price' => 415000],
            ['name' => 'Paket Midsole Non-Flat + Alas', 'category' => 'Paket', 'price' => 435000],
            ['name' => 'Paket Foxing + Alas', 'category' => 'Paket', 'price' => 275000],
            ['name' => 'Paket Lining + Padded Collar', 'category' => 'Paket', 'price' => 275000],
            ['name' => 'Paket Stabilizer + Lem Alas', 'category' => 'Paket', 'price' => 225000],

            // TAMBAHAN
            ['name' => 'Resize', 'category' => 'Tambahan', 'price' => 225000],
            ['name' => 'Resize (Add On)', 'category' => 'Tambahan', 'price' => 125000],
            ['name' => 'Bongkar Sol (Add On)', 'category' => 'Tambahan', 'price' => 125000],
            ['name' => 'Cetak Shoelast', 'category' => 'Tambahan', 'price' => 150000],
            ['name' => 'Sole Swap', 'category' => 'Tambahan', 'price' => 210000],
            ['name' => 'Sepatu Premium (Untuk Swap)', 'category' => 'Tambahan', 'price' => 900000],

            // AKSESORIS
            ['name' => 'Velcro', 'category' => 'Aksesoris', 'price' => 175000],
            ['name' => 'Karet Elastis', 'category' => 'Aksesoris', 'price' => 150000],
            ['name' => 'Ganti Zipper', 'category' => 'Aksesoris', 'price' => 175000],
            ['name' => 'Custom Zipper', 'category' => 'Aksesoris', 'price' => 225000],
            ['name' => 'Ganti BOA Lacing', 'category' => 'Aksesoris', 'price' => 600000],
            ['name' => 'Service BOA Lacing', 'category' => 'Aksesoris', 'price' => 275000],
            ['name' => 'Eyelet per-set', 'category' => 'Aksesoris', 'price' => 150000],
            ['name' => 'Hook & D-Ring/pcs', 'category' => 'Aksesoris', 'price' => 25000],
            ['name' => 'Toe Cap', 'category' => 'Aksesoris', 'price' => 75000],
            ['name' => 'Steel Toe', 'category' => 'Aksesoris', 'price' => 175000],
            ['name' => 'Pengait Lidah', 'category' => 'Aksesoris', 'price' => 125000],
            ['name' => 'Ganti/Tambah TA', 'category' => 'Aksesoris', 'price' => 175000],
        ];

        foreach ($services as $s) {
            Service::updateOrCreate(['name' => $s['name']], $s);
        }
    }
}
