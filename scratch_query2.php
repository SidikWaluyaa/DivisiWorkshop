<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;

$stages = [
    ['name' => 'prep', 'next' => 'sortir'],
    ['name' => 'sortir', 'next' => 'prod'],
    ['name' => 'prod', 'next' => 'qc'],
    ['name' => 'qc', 'next' => 'packing'], // assuming packing is next, or maybe just look at status
];

foreach ($stages as $s) {
    $currentOut = $s['name'] . '_out';
    $nextIn = $s['next'] ? $s['next'] . '_in' : null;

    echo strtoupper($s['name']) . " -> " . strtoupper($s['next']) . " Discrepancies:\n";
    
    $query = WorkOrder::whereNotNull($currentOut);
    if ($nextIn) {
        // Need to see if there's a column for nextIn
        if (\Schema::hasColumn('work_orders', $nextIn)) {
            $query->whereNull($nextIn);
        } else {
            echo "Column $nextIn does not exist.\n";
            continue;
        }
    }

    $discrepancies = $query->select('status', \DB::raw('count(*) as total'))
                           ->groupBy('status')
                           ->get();

    if ($discrepancies->isEmpty()) {
        echo "  None.\n";
    } else {
        foreach ($discrepancies as $d) {
            echo "  Status: {$d->status} -> {$d->total} SPK\n";
        }
    }
    echo "\n";
}
