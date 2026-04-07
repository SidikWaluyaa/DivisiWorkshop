<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\WorkOrderService;

echo "INVESTIGASI TIMESTAMP GHOST:\n";
echo "============================\n";

$ghosts = ['0306-QA', '0394-AW'];

foreach ($ghosts as $spk) {
    echo "\nSPK: $spk\n";
    $wo = WorkOrder::where('spk_number', 'LIKE', "%$spk%")->first();
    if ($wo) {
        $services = WorkOrderService::where('work_order_id', $wo->id)->get();
        foreach ($services as $s) {
            echo "- Jasa: [" . ($s->custom_service_name ?: $s->category_name) . "]\n";
            echo "  Created: " . $s->created_at . "\n";
            echo "  Cost: " . $s->cost . "\n";
        }
    } else {
        echo "WO Not found.\n";
    }
}
