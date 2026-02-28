<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$duplicates = \App\Models\CsSpk::select('spk_number', \DB::raw('count(*) as count'))
    ->groupBy('spk_number')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "No duplicate SPK numbers in cs_spk." . PHP_EOL;
} else {
    foreach ($duplicates as $dup) {
        echo "SPK: " . $dup->spk_number . " Count: " . $dup->count . PHP_EOL;
    }
}
