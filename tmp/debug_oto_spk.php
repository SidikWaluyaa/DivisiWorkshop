<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\WorkOrder;
use App\Models\OTO;
use Carbon\Carbon;

$spk = 'S-2602-12-0004-SW';
$wo = WorkOrder::where('spk_number', $spk)->first();

if (!$wo) {
    die("Work Order $spk not found!\n");
}

echo "--- OTO DEEP DIVE: $spk ---\n";
$otos = OTO::where('work_order_id', $wo->id)->get();

if ($otos->isEmpty()) {
    echo "No OTO records found for this SPK.\n";
}

foreach ($otos as $oto) {
    echo "Proposed: |{$oto->proposed_services}|\n";
    echo "Total Price: |{$oto->total_oto_price}|\n";
    echo "Status: |{$oto->status}|\n";
    echo "Customer Responded At: |{$oto->customer_responded_at}|\n";
    echo "Created By: |{$oto->created_by}|\n";
    echo "CX Assigned To: |{$oto->cx_assigned_to}|\n";
    echo "CX Contacted At: |{$oto->cx_contacted_at}|\n";
    echo "---------------------------------\n";
}
