<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$otos = DB::table('otos')->get();
$formatPrice = fn($val) => 'Rp. ' . number_format((float)$val, 0, ',', '.');

foreach ($otos as $oto) {
    $updates = [];

    // Transform proposed_services
    $services = json_decode($oto->proposed_services, true);
    if (is_array($services)) {
        $names = array_map(fn($s) => $s['service_name'] ?? $s['name'] ?? 'Unknown Service', $services);
        $updates['proposed_services'] = implode(', ', $names);
    }

    // Transform prices if they are still numeric strings
    if (is_numeric($oto->total_normal_price)) {
        $updates['total_normal_price'] = $formatPrice($oto->total_normal_price);
    }
    if (is_numeric($oto->total_oto_price)) {
        $updates['total_oto_price'] = $formatPrice($oto->total_oto_price);
    }
    if (is_numeric($oto->total_discount)) {
        $updates['total_discount'] = $formatPrice($oto->total_discount);
    }
    if (isset($oto->dp_required) && is_numeric($oto->dp_required)) {
        $updates['dp_required'] = $formatPrice($oto->dp_required);
    }

    if (!empty($updates)) {
        DB::table('otos')->where('id', $oto->id)->update($updates);
        echo "Updated OTO ID {$oto->id}\n";
    }
}

echo "Transformation complete.\n";
