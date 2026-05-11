<?php

use App\Models\OrderPayment;
use App\Models\InvoicePayment;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Patch Process...\n";

$invoicePayments = InvoicePayment::whereNull('type')->orWhere('type', '')->get();
$patchedCount = 0;

foreach ($invoicePayments as $ip) {
    $op = OrderPayment::where('invoice_id', $ip->invoice_id)
        ->where('amount_total', $ip->amount)
        ->whereDate('paid_at', $ip->payment_date)
        ->first();

    if ($op && $op->type) {
        echo "Patching ID {$ip->id}: Setting type to {$op->type}\n";
        $ip->update(['type' => $op->type]);
        $patchedCount++;
    }
}

echo "Patch Completed. Total patched: {$patchedCount}\n";
