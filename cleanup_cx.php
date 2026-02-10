<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Running DEFINITIVE CX Cleanup...\n";

// 1. Find WorkOrders with multiple OPEN issues
$multiOpen = DB::table('cx_issues')
    ->where('status', 'OPEN')
    ->select('work_order_id', DB::raw('count(*) as count'))
    ->groupBy('work_order_id')
    ->having('count', '>', 1)
    ->get();

echo "Found " . count($multiOpen) . " WorkOrders with multiple OPEN issues.\n";

foreach ($multiOpen as $row) {
    $orderId = $row->work_order_id;
    $count = $row->count;
    
    // Get all open issues for this WO, sorted by latest
    $issues = DB::table('cx_issues')
        ->where('work_order_id', $orderId)
        ->where('status', 'OPEN')
        ->orderBy('id', 'desc')
        ->get();
    
    $latest = $issues->shift(); // Keep the one with highest ID (latest)
    
    echo "Processing WO ID: {$orderId}. Keeping Issue ID: {$latest->id}. Closing " . count($issues) . " old issues.\n";
    
    foreach ($issues as $old) {
        DB::table('cx_issues')->where('id', $old->id)->update([
            'status' => 'RESOLVED',
            'resolution' => 'SUPERSEDED_AUTO',
            'resolution_notes' => 'Tutup otomatis karena ada laporan lebih baru.',
            'resolved_at' => now(),
        ]);
    }
}

// 2. Clear old notes if they are just repeats (Tough to do safely, so I'll just clean the stuck status)

// 3. Fix stuck status (CX_FOLLOWUP but 0 OPEN issues)
$stuck = DB::table('work_orders')
    ->whereIn('status', ['CX_FOLLOWUP', 'HOLD_FOR_CX'])
    ->get();

foreach ($stuck as $wo) {
    $openCount = DB::table('cx_issues')->where('work_order_id', $wo->id)->where('status', 'OPEN')->count();
    if ($openCount === 0 && empty($wo->reception_rejection_reason)) {
        echo "Resetting SPK: {$wo->spk_number} as it has no open issues.\n";
        DB::table('work_orders')->where('id', $wo->id)->update([
            'status' => 'ASSESSMENT' // Default fallback
        ]);
    }
}

echo "Cleanup Finished!\n";
