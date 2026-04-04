<?php

include 'vendor/autoload.php';
$app = include_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\OTO;
use Carbon\Carbon;

$start = '2026-03-01 00:00:00';
$end = '2026-03-31 23:59:59';

echo "--- ACCEPTED OTOs IN MARCH ---" . PHP_EOL;
$otos = OTO::where('status', 'ACCEPTED')
    ->whereBetween('customer_responded_at', [$start, $end])
    ->get();

echo "Total: " . $otos->count() . PHP_EOL;
foreach ($otos as $o) {
    $isCx = ($o->cx_assigned_to || $o->cx_contacted_at);
    echo "  ID: {$o->id} | Assigned: " . ($o->cx_assigned_to ?? 'NULL') . " | Contacted: " . ($o->cx_contacted_at ?? 'NULL') . " | Managed by CX: " . ($isCx ? 'YES' : 'NO') . PHP_EOL;
}
