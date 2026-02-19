<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$otos = DB::table('otos')->get();

foreach ($otos as $oto) {
    $order = DB::table('work_orders')->where('id', $oto->work_order_id)->first();
    
    if ($order) {
        DB::table('otos')->where('id', $oto->id)->update([
            'spk_number' => $order->spk_number,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
        ]);
        echo "Backfilled OTO ID {$oto->id} with SPK {$order->spk_number}\n";
    }
}

echo "Backfill complete.\n";
