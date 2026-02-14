<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderPayment;
use App\Models\WorkOrder;

class BackfillOrderPaymentSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-order-payment-snapshots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfills missing snapshot data for legacy order payment records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $payments = OrderPayment::whereNull('spk_number_snapshot')->get();

        $this->info("Found " . $payments->count() . " payments to backfill.");

        foreach ($payments as $payment) {
            $workOrder = $payment->workOrder;
            
            if (!$workOrder) {
                $this->warn("Payment ID {$payment->id} has no associated WorkOrder. Skipping.");
                continue;
            }

            $servicesSummary = $workOrder->workOrderServices->map(function($ws) use ($workOrder) {
                $name = $ws->custom_service_name ?? ($ws->service ? $ws->service->name : 'Layanan');
                return "{$workOrder->shoe_brand} - {$name} (Rp " . number_format($ws->cost, 0, ',', '.') . ")";
            })->implode("\n");

            // For legacy data, we approximate the balance snapshot as the balance at that time might not be perfectly traceable 
            // without complex event-sourcing. We'll use the current sisa_tagihan for legacy reference.
            $payment->update([
                'spk_number_snapshot' => $workOrder->spk_number,
                'services_snapshot' => $servicesSummary,
                'customer_name_snapshot' => $workOrder->customer_name,
                'customer_phone_snapshot' => $workOrder->customer_phone,
                'total_bill_snapshot' => $workOrder->total_transaksi,
                'discount_snapshot' => $workOrder->discount ?? 0,
                'shipping_cost_snapshot' => $workOrder->shipping_cost ?? 0,
                // Best effort for legacy balance: use current balance if it's the latest, or null
                'balance_snapshot' => $workOrder->sisa_tagihan 
            ]);

            $this->line("Backfilled Payment ID: {$payment->id} (SPK: {$workOrder->spk_number})");
        }

        $this->info("Backfill complete!");
    }
}
