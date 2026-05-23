<?php
$start = today()->startOfDay();
$end = today()->endOfDay();

$total = \App\Models\WorkOrder::whereNotNull('entry_date')
    ->whereBetween('entry_date', [$start, $end])
    ->count();

$fixed = \App\Models\WorkOrder::whereNotNull('entry_date')
    ->whereBetween('entry_date', [$start, $end])
    ->where('status', '!=', \App\Enums\WorkOrderStatus::SPK_PENDING)
    ->count();

$pendingCount = \App\Models\WorkOrder::whereNotNull('entry_date')
    ->whereBetween('entry_date', [$start, $end])
    ->where('status', \App\Enums\WorkOrderStatus::SPK_PENDING)
    ->count();

echo "Sebelum fix (termasuk SPK_PENDING): {$total}\n";
echo "Sesudah fix (exclude SPK_PENDING): {$fixed}\n";
echo "SPK_PENDING yang tidak terhitung lagi: {$pendingCount}\n";
