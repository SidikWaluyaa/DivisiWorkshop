<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\CxDashboardService;
use Carbon\Carbon;

$service = new CxDashboardService();
$start = Carbon::parse('2026-04-04')->startOfDay();
$end = Carbon::parse('2026-04-04')->endOfDay();

// Get the data using the final precision logic
$data = $service->getSummary($start, $end, true);

// Calculate consolidated summary like the API Controller does
$totalNominal = ($data['upsell']['total_nominal'] ?? 0) + ($data['upsell']['oto_nominal'] ?? 0);
$totalVolume = ($data['upsell']['total_volume'] ?? 0) + ($data['upsell']['oto_volume'] ?? 0);
$combinedArpu = $totalVolume > 0 ? $totalNominal / $totalVolume : 0;

$jsonResponse = [
    'status' => 'success',
    'summary' => [
        'total_nominal' => (float)$totalNominal,
        'total_volume' => (int)$totalVolume,
        'combined_arpu' => (float)$combinedArpu,
        'currency' => 'IDR'
    ],
    'period' => $data['period'],
    'data' => [
        'kpi' => $data['kpi'],
        'upsell' => [
            'total_nominal' => $data['upsell']['total_nominal'],
            'total_volume' => $data['upsell']['total_volume'],
            'tambah_jasa_items' => $data['upsell']['tambah_jasa_items']
        ]
    ]
];

echo json_encode($jsonResponse, JSON_PRETTY_PRINT);
echo "\n";
