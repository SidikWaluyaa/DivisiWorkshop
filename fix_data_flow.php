<?php

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Data Remediation for Work Order Status Flow...\n";

DB::transaction(function () {
    // 1. Identify orders in PREPARATION status but missing a manifest
    $targetOrders = WorkOrder::where('status', WorkOrderStatus::PREPARATION)
        ->whereNull('workshop_manifest_id')
        ->get();

    echo "Found " . $targetOrders->count() . " orders in PREPARATION without a manifest.\n";

    foreach ($targetOrders as $order) {
        echo "Processing Order #{$order->spk_number}...\n";

        // 2. Revert status to READY_TO_DISPATCH
        $order->update([
            'status' => WorkOrderStatus::READY_TO_DISPATCH,
            'current_location' => 'Gudang (Remediasi)',
        ]);

        // 3. Add Log
        $order->logs()->create([
            'step' => 'REMEDIATION',
            'action' => 'REVERT_TO_DISPATCH',
            'user_id' => null, // System
            'description' => "Otomatis: Mengembalikan order ke antrian Manifest karena terdeteksi melewati alur logistik."
        ]);
    }
});

echo "Remediation Complete!\n";
