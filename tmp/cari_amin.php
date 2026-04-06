<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;

echo "PENCARIAN KHUSUS AMIN (0314):\n";
echo "=============================\n";

$wos = WorkOrder::where('spk_number', 'LIKE', '%0314%')->get();
if ($wos->isEmpty()) {
    echo "TIDAK DITEMUKAN SPK DENGAN DIGIT 0314!\n";
} else {
    foreach ($wos as $wo) {
        $statusStr = $wo->status instanceof \BackedEnum ? $wo->status->value : $wo->status;
        echo "- SPK: {$wo->spk_number} | Status: {$statusStr} | Created: {$wo->created_at}\n";
    }
}
