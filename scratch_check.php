<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new App\Services\WarehouseApiService();
$data = $service->getSortirIntelligenceData();
$resource = \App\Http\Resources\V1\WarehouseSortirResource::collection($data);

echo json_encode($resource->response()->getData()->data[0], JSON_PRETTY_PRINT);
