<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;

$spks = [
    '0314-AW', // Amin
    '0307-QA', // Akbar
    '0900-RQ', // Rony
    '0394-AW', // Ryan
];

echo "DUMP JASA EKSAK:\n";
echo "================\n";

foreach ($spks as $part) {
    $wo = WorkOrder::where('spk_number', 'LIKE', "%{$part}%")->first();
    if ($wo) {
        echo "\nSPK: {$wo->spk_number}\n";
        foreach ($wo->workOrderServices as $s) {
            $name = $s->custom_service_name ?: $s->category_name;
            echo "- Nama: [{$name}] | Nominal: " . number_format($s->cost) . " | Created: {$s->created_at}\n";
        }
    } else {
        echo "\nSPK {$part} TIDAK DITEMUKAN!\n";
    }
}
