<?php

use App\Models\OrderPayment;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Sync Process...\n";

$orderPayments = OrderPayment::whereNotNull('invoice_id')->get();
$syncedCount = 0;

foreach ($orderPayments as $op) {
    $exists = InvoicePayment::where('invoice_id', $op->invoice_id)
        ->where('amount', $op->amount_total)
        ->whereDate('payment_date', $op->paid_at)
        ->exists();

    if (!$exists) {
        echo "Orphaned Payment Found: ID {$op->id} for Invoice {$op->invoice_id} Amount {$op->amount_total}\n";
        
        InvoicePayment::create([
            'invoice_id' => $op->invoice_id,
            'amount' => $op->amount_total,
            'payment_date' => $op->paid_at,
            'notes' => $op->notes ?? 'Auto-synced from OrderPayment',
            'verified' => $op->is_verified,
            'type' => $op->type,
            'created_by' => $op->pic_id ?? 1
        ]);
        
        echo "Successfully Synced to InvoicePayment.\n";
        $syncedCount++;
    }
}

echo "Sync Completed. Total synced: {$syncedCount}\n";
