<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$services = \App\Models\Service::select('id', 'name', 'category')->get();
foreach ($services as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Cat: {$s->category}\n";
}
