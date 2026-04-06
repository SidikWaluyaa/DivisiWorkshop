<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\WorkOrderService;

echo "MENCARI JASA 225.000 UNTUK AMIN:\n";
echo "===============================\n";

// Cari di SPK satunya
$wo2 = WorkOrder::where('spk_number', 'S-2602-17-0314-QA')->first();
if ($wo2) {
    echo "Cek SPK S-2602-17-0314-QA:\n";
    foreach ($wo2->workOrderServices as $s) {
        $name = $s->custom_service_name ?: $s->category_name;
        echo "- [{$name}] | Rp " . number_format($s->cost) . "\n";
    }
}

// Cari jasa 225.000 secara umum yang dibuat di sekitar Maret/April
echo "\nJasa 225.000 di seluruh database (terbaru):\n";
$services = WorkOrderService::where('cost', 225000)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($services as $s) {
    $wo = $s->workOrder;
    $name = $s->custom_service_name ?: $s->category_name;
    echo "- SPK: {$wo->spk_number} | Jasa: [{$name}] | Created: {$s->created_at}\n";
}
