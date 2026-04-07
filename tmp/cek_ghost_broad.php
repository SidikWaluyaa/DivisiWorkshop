<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\WorkOrderService;

echo "BROAD SEARCH FOR GHOSTS:\n";
echo "========================\n";

$ghosts = ['0306', '0394'];

foreach ($ghosts as $id) {
    echo "\nSearching ID: $id\n";
    $wo = WorkOrder::where('spk_number', 'LIKE', "%-$id-%")->first();
    if ($wo) {
        echo "Found SPK: " . $wo->spk_number . " (ID: $wo->id)\n";
        $services = WorkOrderService::where('work_order_id', $wo->id)->get();
        foreach ($services as $s) {
            echo "- Jasa: [" . ($s->custom_service_name ?: $s->category_name) . "]\n";
            echo "  Created: " . $s->created_at . "\n";
            echo "  Cost: " . $s->cost . "\n";
        }
    } else {
        echo "WO with ID $id not found.\n";
    }
}
