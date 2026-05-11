<?php
use App\Models\Invoice;
use App\Models\OrderPayment;
use App\Models\InvoicePayment;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$inv = Invoice::find(22);
if($inv) {
    OrderPayment::where('invoice_id', 22)->update(['is_verified' => true]);
    InvoicePayment::where('invoice_id', 22)->update(['verified' => true]);
    $inv->syncFinancials();
    echo "Invoice 22 auto-verified and synced.\n";
}
