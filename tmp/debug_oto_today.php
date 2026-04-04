<?php

include 'vendor/autoload.php';
$app = include_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\OTO;
use Carbon\Carbon;

$today = Carbon::now()->format('Y-m-d');
$start = Carbon::now()->startOfDay();
$end = Carbon::now()->endOfDay();

echo "--- TODAY'S OTO (April 4th) ---" . PHP_EOL;
$otos = OTO::where('status', 'ACCEPTED')
    ->whereBetween('customer_responded_at', [$start, $end])
    ->get();

echo "Total Today: " . $otos->count() . PHP_EOL;
foreach ($otos as $o) {
    echo "  ID: {$o->id} | Title: {$o->title} | Assigned: {$o->cx_assigned_to} | Contacted: {$o->cx_contacted_at} | CreatedBy: {$o->created_by}" . PHP_EOL;
    echo "  Services: {$o->proposed_services}" . PHP_EOL;
}
