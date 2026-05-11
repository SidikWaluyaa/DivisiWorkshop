<?php

use App\Models\OrderPayment;
use App\Models\InvoicePayment;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Retroactive Verification Sync...\n";

$verifiedInvoicePayments = InvoicePayment::where('verified', true)->get();
$count = 0;

foreach ($verifiedInvoicePayments as $ip) {
    $updated = OrderPayment::where('invoice_id', $ip->invoice_id)
        ->where('amount_total', $ip->amount)
        ->whereDate('paid_at', $ip->payment_date)
        ->update(['is_verified' => true]);

    if ($updated) {
        if ($ip->invoice) {
            $ip->invoice->syncFinancials();
        }
        $count++;
    }
}

echo "Retroactive Sync Completed. Total records updated: {$count}\n";
