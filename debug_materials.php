<?php

use App\Models\Material;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking Material Types...\n";

$types = DB::table('materials')->select('type', DB::raw('count(*) as total'))->groupBy('type')->get();

foreach ($types as $t) {
    echo "Type: '{$t->type}' - Count: {$t->total}\n";
}

echo "\nChecking Sub-Categories for Material Sol:\n";
$subs = DB::table('materials')->where('type', 'Material Sol')->select('sub_category', DB::raw('count(*) as total'))->groupBy('sub_category')->get();
foreach ($subs as $s) {
    echo "Sub: '{$s->sub_category}' - Count: {$s->total}\n";
}

echo "\nTotal Material Sol via Eloquent: " . Material::where('type', 'Material Sol')->count() . "\n";
