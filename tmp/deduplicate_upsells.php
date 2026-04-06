<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CxIssue;
use Illuminate\Support\Facades\DB;

echo "DEDUPLICATING UPSELL TICKETS:\n";
echo "=============================\n";

// Find Work Orders that have multiple 'tambah_jasa' issues
$duplicates = DB::table('cx_issues')
    ->select('work_order_id', DB::raw('count(*) as count'))
    ->where('resolution_type', 'tambah_jasa')
    ->groupBy('work_order_id')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "No duplicates found.\n";
}

foreach ($duplicates as $dup) {
    echo "Processing WO ID: {$dup->work_order_id} (Found {$dup->count} upsell tickets)\n";
    
    // Get all upsell issues for this WO, ordered by ID asc
    $issues = CxIssue::where('work_order_id', $dup->work_order_id)
        ->where('resolution_type', 'tambah_jasa')
        ->orderBy('id', 'asc')
        ->get();
    
    // Keep the FIRST one as 'tambah_jasa', set others to 'lanjut'
    $keepId = $issues->first()->id;
    
    foreach ($issues as $idx => $issue) {
        if ($idx > 0) {
            $issue->update(['resolution_type' => 'lanjut']);
            echo "  - Issue #{$issue->id} set to 'lanjut' (Deduplicated)\n";
        } else {
            echo "  - Issue #{$issue->id} kept as 'tambah_jasa'\n";
        }
    }
}

echo "\nDE-DUPLICATION COMPLETE!\n";
