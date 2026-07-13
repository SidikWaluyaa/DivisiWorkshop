<?php

// Load Laravel Bootstrap
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WorkOrder;
use App\Models\CxIssue;
use App\Enums\WorkOrderStatus;

echo "--- CLEANING UP OPEN CX ISSUES FOR FINISHED/INACTIVE ORDERS ---\n";

// Find all work orders that are already finished, delivered, archived, or cancelled
$inactiveStatuses = [
    WorkOrderStatus::SELESAI->value,
    WorkOrderStatus::DIANTAR->value,
    WorkOrderStatus::HISTORY->value,
    WorkOrderStatus::BATAL->value
];

// Find open CX issues belonging to these inactive work orders
$openIssuesToResolve = CxIssue::where('status', 'OPEN')
    ->whereHas('workOrder', function($q) use ($inactiveStatuses) {
        $q->whereIn('status', $inactiveStatuses);
    })->get();

$count = $openIssuesToResolve->count();
echo "Found $count open issues that belong to completed/inactive orders.\n";

if ($count > 0) {
    foreach ($openIssuesToResolve as $issue) {
        $order = $issue->workOrder;
        echo "- Resolving Issue ID: {$issue->id} (Category: {$issue->category}) for SPK: {$order->spk_number} (Status: {$order->status->value})\n";
        
        $issue->update([
            'status' => 'RESOLVED',
            'resolved_by' => 1, // Default Admin/System
            'resolved_at' => now(),
            'resolution_notes' => "Diselesaikan otomatis oleh sistem pembersih karena status utama SPK sudah {$order->status->value}."
        ]);
    }
    echo "\nSuccessfully resolved all $count open issues.\n";
} else {
    echo "No open issues found for completed/inactive orders. Database is clean!\n";
}
