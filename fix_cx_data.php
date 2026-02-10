<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

echo "Starting CX Data Fix...\n";

// Get all issues where SPK is missing
$issues = DB::table('cx_issues')
    ->whereNull('spk_number')
    ->orWhere('spk_number', '')
    ->get();

$count = 0;
foreach ($issues as $issue) {
    if ($issue->work_order_id) {
        $wo = DB::table('work_orders')->where('id', $issue->work_order_id)->first();
        
        if ($wo) {
            DB::table('cx_issues')
                ->where('id', $issue->id)
                ->update([
                    'spk_number' => $wo->spk_number,
                    'customer_name' => $wo->customer_name,
                    'customer_phone' => $wo->customer_phone,
                    'updated_at' => now()
                ]);
            
            $count++;
            echo "Fixed Issue #{$issue->id} -> SPK: {$wo->spk_number}\n";
        }
    }
}

echo "Done! fixed {$count} records.\n";
