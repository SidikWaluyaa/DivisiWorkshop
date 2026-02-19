<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OTO;

$total = OTO::count();
$active = OTO::count();
$deleted = 0;
$cancelledStatus = OTO::where('status', 'CANCELLED')->count();
$cancelledAndDeleted = 0;
$cancelledNotDeleted = OTO::where('status', 'CANCELLED')->count();

echo "Total OTOs (including trashed): $total\n";
echo "Active OTOs: $active\n";
echo "Soft Deleted OTOs: $deleted\n";
echo "Status CANCELLED: $cancelledStatus\n";
echo "Status CANCELLED & Soft Deleted: $cancelledAndDeleted\n";
echo "Status CANCELLED & NOT Deleted: $cancelledNotDeleted\n";

if ($cancelledNotDeleted > 0) {
    echo "\nSample of CANCELLED but NOT Deleted:\n";
    $samples = OTO::withTrashed()->where('status', 'CANCELLED')->whereNull('deleted_at')->limit(5)->get();
    foreach ($samples as $s) {
        echo "- ID: {$s->id}, WO: {$s->work_order_id}, Created: {$s->created_at}\n";
    }
}
