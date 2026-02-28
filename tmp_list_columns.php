<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = \Schema::getColumnListing('work_orders');
foreach ($columns as $column) {
    echo $column . PHP_EOL;
}
