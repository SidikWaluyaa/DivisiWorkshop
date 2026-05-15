<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WorkOrder;

// Cari secara manual menggunakan raw query agar lebih akurat
$orders = WorkOrder::where('previous_status', 'LIKE', '%CX_FOLLOWUP%')
    ->orWhere('spk_number', 'S-2605-07-0356-SB')
    ->orWhere('spk_number', 'S-2605-07-0374-MY')
    ->get();

echo "Menemukan " . $orders->count() . " order untuk dikoreksi.\n";

foreach ($orders as $order) {
    $hasMaterialIssue = $order->cxIssues()->where('category', 'MATERIAL')->exists();
    $newPreStatus = $hasMaterialIssue ? 'SORTIR' : 'PRODUCTION';
    
    // Update langsung ke database
    $order->update(['previous_status' => $newPreStatus]);
    echo "BERHASIL: {$order->spk_number} | Status Sekarang: {$order->status->value} | Pre Status diperbaiki ke: {$newPreStatus}\n";
}

echo "Proses selesai.\n";
