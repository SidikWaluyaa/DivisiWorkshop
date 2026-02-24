<?php

namespace App\Console\Commands\Finance;

use Illuminate\Console\Command;
use App\Models\WorkOrder;
use Carbon\Carbon;

class CleanupUniqueCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:cleanup-unique-codes {--days=7 : Expiration days for abandoned orders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup unique codes for free orders (0 Rp) or expired abandoned orders.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Unique Code Cleanup...');

        // 1. Cleanup for 0 Rp orders (Always NULL regardless of age)
        $zeroBillOrders = WorkOrder::whereNotNull('unique_code')
            ->where('total_transaksi', '<=', 1000) // 1000 is effectively 0 if it only has code 100
            ->get();

        $countZero = 0;
        foreach ($zeroBillOrders as $order) {
            // Re-check base bill
            $jasa = $order->workOrderServices()->sum('cost');
            $oto = $order->cost_oto ?? 0;
            $add = $order->cost_add_service ?? 0;
            $ongkir = $order->shipping_cost ?? 0;
            $discount = $order->discount ?? 0;
            $baseTotal = ($jasa + $oto + $add + $ongkir) - $discount;

            if ($baseTotal <= 0) {
                $order->update(['unique_code' => null]);
                $countZero++;
            }
        }
        $this->comment("✓ Cleaned up {$countZero} codes from Rp 0 orders.");

        // 2. Cleanup for Expired Abandoned orders (No payment for X days)
        $days = (int) $this->option('days');
        $expiryDate = Carbon::now()->subDays($days);

        $expiredOrders = WorkOrder::whereNotNull('unique_code')
            ->where('status_pembayaran', 'Belum Bayar')
            ->where('updated_at', '<', $expiryDate)
            ->where('total_paid', '<=', 0)
            ->get();

        $countExpired = 0;
        foreach ($expiredOrders as $order) {
            $order->update(['unique_code' => null]);
            $countExpired++;
        }
        $this->comment("✓ Cleaned up {$countExpired} codes from abandoned orders (unpaid for > {$days} days).");

        $this->info('Cleanup process completed.');
    }
}
