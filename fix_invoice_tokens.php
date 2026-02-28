<?php

/**
 * One-off script to migrate existing Invoice URLs to the new Secure Token format.
 * Use this as an alternative to 'php artisan tinker'.
 */

use Illuminate\Contracts\Console\Kernel;
use App\Models\Invoice;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "--- Memulai Update Token Invoice ---\n";

$invoices = Invoice::all();
$count = $invoices->count();

foreach ($invoices as $index => $invoice) {
    echo "[" . ($index + 1) . "/$count] Mengupdate: " . $invoice->invoice_number . "... ";
    try {
        $invoice->syncFinancials();
        echo "BERHASIL\n";
    } catch (\Exception $e) {
        echo "GAGAL: " . $e->getMessage() . "\n";
    }
}

echo "--- Update Selesai! ---\n";
echo "Silakan hapus file ini demi keamanan: rm fix_invoice_tokens.php\n";
