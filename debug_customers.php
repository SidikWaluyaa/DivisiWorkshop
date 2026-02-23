<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;

echo "--- CUSTOMER DATA DIAGNOSTICS ---\n";

$activeCount = Customer::count();
$trashedCount = Customer::onlyTrashed()->count();
$totalCount = $activeCount + $trashedCount;

$woCount = WorkOrder::count();
$woTrashedCount = WorkOrder::onlyTrashed()->count();

echo "Active Customers: $activeCount\n";
echo "Trashed Customers: $trashedCount\n";
echo "Total in Pool: $totalCount\n";
echo "Active WorkOrders: $woCount\n";
echo "Trashed WorkOrders: $woTrashedCount\n";

echo "\n--- BY CREATION DATE (LAST 30 DAYS) ---\n";
$stats = Customer::withTrashed()
    ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'), DB::raw('count(deleted_at) as deleted'))
    ->where('created_at', '>=', now()->subDays(30))
    ->groupBy('date')
    ->orderBy('date', 'desc')
    ->get();

foreach ($stats as $stat) {
    echo "{$stat->date}: Total {$stat->count} (Deleted: {$stat->deleted})\n";
}

echo "\n--- LATEST CUSTOMERS ---\n";
$latest = Customer::latest()->limit(5)->get();
foreach ($latest as $c) {
    echo "#{$c->id}: {$c->name} ({$c->phone}) - Created: {$c->created_at}\n";
}

echo "\n--- ORPHANED WORK ORDERS (NO CUSTOMER RECORD) ---\n";
$orphans = WorkOrder::whereNotNull('customer_phone')
    ->whereNotExists(function ($query) {
        $query->select(DB::raw(1))
            ->from('customers')
            ->whereRaw('customers.phone = work_orders.customer_phone');
    })
    ->select('customer_phone', DB::raw('count(*) as count'))
    ->groupBy('customer_phone')
    ->orderBy('count', 'desc')
    ->limit(10)
    ->get();

echo "Unique orphan phones: " . count($orphans) . "\n";
foreach ($orphans as $o) {
    echo "Phone: {$o->customer_phone} (WO Count: {$o->count})\n";
}

echo "\n--- FIRST 20 WORK ORDERS ---\n";
$firstOrders = WorkOrder::orderBy('id', 'asc')->limit(20)->get();
foreach ($firstOrders as $o) {
    echo "#{$o->id} | {$o->spk_number} | {$o->created_at} | Phone: {$o->customer_phone}\n";
}

echo "--- END DIAGNOSTICS ---\n";
