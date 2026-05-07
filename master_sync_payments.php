<?php

use App\Models\Invoice;
use App\Models\OrderPayment;
use App\Models\InvoicePayment;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Master Sync (Invoice Based)...\n";

$invoices = Invoice::all();
$totalSynced = 0;

foreach ($invoices as $invoice) {
    echo "Processing Invoice: {$invoice->invoice_number} (ID: {$invoice->id})\n";
    
    // Get all payments for this invoice from order_payments
    $orderPayments = OrderPayment::where('invoice_id', $invoice->id)->get();
    
    foreach ($orderPayments as $op) {
        // Check if this payment exists in invoice_payments
        $exists = InvoicePayment::where('invoice_id', $invoice->id)
            ->where('amount', $op->amount_total)
            ->whereDate('payment_date', $op->paid_at)
            ->exists();
            
        if (!$exists) {
            echo "  [MISSING] Payment found in order_payments (ID: {$op->id}) but NOT in invoice_payments. Syncing...\n";
            
            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $op->amount_total,
                'payment_date' => $op->paid_at,
                'notes' => $op->notes ?? 'Auto-synced from OrderPayment via Master Sync',
                'verified' => $op->is_verified,
                'type' => $op->type,
                'created_by' => $op->pic_id ?? 1
            ]);
            
            echo "  [SUCCESS] Synced.\n";
            $totalSynced++;
        }
    }
    
    // Final sync for invoice financials
    $invoice->syncFinancials();
}

echo "\nMaster Sync Completed. Total records synced: {$totalSynced}\n";
