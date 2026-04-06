<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;

echo "FINAL CHECK JASA 225.000:\n";
echo "=========================\n";

// 1. Cek Akbar 0306 (Yang sebelumnya 0 Jasa di Audit)
$wo_akbar = WorkOrder::where('spk_number', 'LIKE', '%0306-QA%')->first();
if ($wo_akbar) {
    echo "\nAkbar 0306-QA:\n";
    foreach ($wo_akbar->workOrderServices as $s) {
        $name = $s->custom_service_name ?: $s->category_name;
        echo "- [{$name}] | Rp " . number_format($s->cost) . " | Created: {$s->created_at}\n";
    }
}

// 2. Cek SPK 0584-AW (Siapa tau ini Amin)
$wo_unknown = WorkOrder::where('spk_number', 'LIKE', '%0584-AW%')->first();
if ($wo_unknown) {
    echo "\nSPK 0584-AW (Nama Customer?):\n";
    echo "Customer ID: {$wo_unknown->customer_id}\n"; // Cek ini nanti
    foreach ($wo_unknown->workOrderServices as $s) {
        $name = $s->custom_service_name ?: $s->category_name;
        echo "- [{$name}] | Rp " . number_format($s->cost) . " | Created: {$s->created_at}\n";
    }
}
