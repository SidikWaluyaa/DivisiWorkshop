<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CxIssue;

echo "DEEP INVESTIGATION: ARI (435) & ANDRI (353)\n";
echo "===========================================\n";

$targets = [435, 353];
foreach ($targets as $id) {
    $i = CxIssue::find($id);
    if (!$i) {
        echo "ID {$id} NOT FOUND!\n";
        continue;
    }

    echo "\n[ID {$id}] SPK: " . $i->workOrder->spk_number . "\n";
    echo "CATATAN JAWABAN: '{$i->resolution_notes}'\n";
    echo "WAKTU TIKET DIBUAT: {$i->created_at}\n";
    echo "DAFTAR JASA DI SPK:\n";
    
    foreach ($i->workOrder->workOrderServices as $s) {
        $name = $s->custom_service_name ?: $s->category_name;
        $isAfter = $s->created_at >= $i->created_at;
        echo "- {$name} (Rp " . number_format($s->cost) . ")\n";
        echo "  > Dibuat: {$s->created_at}\n";
        echo "  > Lolos Filter Waktu? " . ($isAfter ? 'YA' : 'TIDAK') . "\n";
    }
}
