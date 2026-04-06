<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CxIssue;

echo "DEEP INVESTIGATION v2: ARI (435)\n";
echo "===============================\n";

$i = CxIssue::find(435);
if (!$i) {
    echo "ID 435 NOT FOUND!\n";
    exit;
}

$wo = $i->workOrder;
echo "SPK: " . $wo->spk_number . "\n";
echo "RESOLUTION NOTES: '{$i->resolution_notes}'\n";
echo "TECHNICIAN NOTES (WO): '{$wo->technician_notes}'\n\n";

echo "DAFTAR JASA:\n";
foreach ($wo->workOrderServices as $s) {
    $name = $s->custom_service_name ?: $s->category_name;
    $isAfter = $s->created_at >= $i->created_at;
    echo "- {$name} (Rp " . number_format($s->cost) . ")\n";
    echo "  > Dibuat: {$s->created_at}\n";
    echo "  > Tiket CX Dibuat: {$i->created_at}\n";
    echo "  > Lolos Filter Waktu? " . ($isAfter ? 'YA' : 'TIDAK') . "\n";
}
