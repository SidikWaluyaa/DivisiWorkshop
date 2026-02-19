<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$columns = Schema::getColumnListing('otos');
echo "Columns in 'otos' table:\n";
foreach ($columns as $column) {
    $type = Schema::getColumnType('otos', $column);
    echo "- $column ($type)\n";
}

echo "\nLatest 5 OTOs data:\n";
$otos = DB::table('otos')->latest()->limit(5)->get();
foreach ($otos as $oto) {
    echo "ID: {$oto->id}\n";
    echo "SPK: {$oto->spk_number}\n";
    echo "Customer: {$oto->customer_name} ({$oto->customer_phone})\n";
    echo "Proposed Services: {$oto->proposed_services}\n";
    echo "Total Normal: {$oto->total_normal_price}\n";
    echo "Total OTO: {$oto->total_oto_price}\n";
    echo "Total Discount: {$oto->total_discount} ({$oto->discount_percent}%)\n";
    echo "-------------------\n";
}
