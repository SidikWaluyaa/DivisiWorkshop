<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkOrder;
use App\Models\Customer;
use App\Helpers\PhoneHelper;

class RestoreMissingCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:restore-missing-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menyalin ulang data customer dari SPK ke Master Data jika Master Datanya hilang/terhapus.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Memeriksa SPK yang tidak memiliki profil Master Customer...");

        // Ambil spk yang nomor HP-nya tidak ada di database pelanggan (yang belum dihapus)
        $orphanedSpks = WorkOrder::whereNotIn('customer_phone', function($query) {
            $query->select('phone')->from('customers')->whereNull('deleted_at');
        })->get();

        if ($orphanedSpks->isEmpty()) {
            $this->info("✔ Semua SPK sudah terhubung dengan Master Customer. Tidak ada data yang hilang.");
            return;
        }

        $this->warn("Ditemukan " . $orphanedSpks->count() . " riwayat SPK yang Master Customernya hilang.");
        $restoredCount = 0;

        foreach ($orphanedSpks as $spk) {
            if (empty($spk->customer_phone) || empty($spk->customer_name)) {
                $this->line("- Mengabaikan SPK {$spk->spk_number} karena data nama/nomor kosong.");
                continue;
            }

            // Normalkan nomor HP sesuai sistem
            $normalizedPhone = PhoneHelper::normalize($spk->customer_phone) ?? $spk->customer_phone;

            // Pastikan belum ada (lagi) di iterasi sebelumnya
            $existing = Customer::withTrashed()->where('phone', $normalizedPhone)->first();
            
            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                    $this->line("- Mengaktifkan kembali (Restore) customer: {$existing->name} - {$normalizedPhone}");
                    $restoredCount++;
                }
            } else {
                // Buat Master Data baru dari informasi SPK ini
                Customer::create([
                    'phone' => $normalizedPhone,
                    'name' => $spk->customer_name,
                    'email' => $spk->customer_email ?? null,
                    'address' => $spk->customer_address ?? null,
                    'city' => $spk->customer_city ?? null,
                    'province' => $spk->customer_province ?? null,
                    'notes' => 'Di-restore otomatis dari SPK: ' . $spk->spk_number,
                ]);
                $this->line("- Berhasil mengekstrak dan membuat Customer baru: {$spk->customer_name} - {$normalizedPhone}");
                $restoredCount++;
            }
        }

        $this->info("Selesai! Berhasil memulihkan (restore) {$restoredCount} profil Master Customer.");
    }
}
