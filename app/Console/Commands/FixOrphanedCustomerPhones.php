<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkOrder;
use App\Models\Customer;

class FixOrphanedCustomerPhones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-orphaned-phones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbaiki SPK yang memiliki customer_phone (nomor HP) lama yang sudah terputus dari tabel Master Customer.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Memulai pengecekan SPK dengan data pelanggan terputus...");

        // Ambil semua SPK yang nomor HP-nya TIDAK ADA di tabel Customers
        $orphanedSpks = WorkOrder::whereNotIn('customer_phone', function($query) {
            $query->select('phone')->from('customers');
        })->get();

        if ($orphanedSpks->isEmpty()) {
            $this->info("✔ Tidak ditemukan SPK yang terputus.");
            return;
        }

        $this->warn("Ditemukan " . $orphanedSpks->count() . " riwayat SPK yang berpotensi putus relasi (menggunakan nomor HP lama).");

        $fixedCount = 0;

        foreach ($orphanedSpks as $spk) {
            // Cari Master Customer berdasarkan NAMA CUSTOMER yang sama persis
            // Jika ada lebih dari satu nama yang sama (misal 2 orang bernama Budi), ambil yang paling baru diupdate
            $matchedCustomer = Customer::where('name', $spk->customer_name)
                                       ->orderBy('updated_at', 'desc')
                                       ->first();

            if ($matchedCustomer) {
                // Update nomor HP di SPK menggunakan nomor HP terbaru di Master Customer
                $oldPhone = $spk->customer_phone;
                $spk->update([
                    'customer_phone' => $matchedCustomer->phone
                ]);
                
                $this->line("- Sinkronisasi SPK [{$spk->spk_number}]: {$spk->customer_name} ( {$oldPhone} -> {$matchedCustomer->phone} )");
                $fixedCount++;
            }
        }

        $this->info("Selesai! Berhasil memperbaiki dan menghubungkan kembali {$fixedCount} SPK.");
    }
}
