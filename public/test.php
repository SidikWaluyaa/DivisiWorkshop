<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h2>TEKNIS</h2><pre>";
$teknis = \App\Models\MasterSolution::where('is_active', true)->where('category', 'TEKNIS')->get();
echo json_encode($teknis, JSON_PRETTY_PRINT);
echo "</pre>";

echo "<h2>MATERIAL</h2><pre>";
$material = \App\Models\MasterSolution::where('is_active', true)->where('category', 'MATERIAL')->get();
echo json_encode($material, JSON_PRETTY_PRINT);
echo "</pre>";
