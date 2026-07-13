<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$mismatchedOrders = App\Models\WorkOrder::whereIn('status', [
        App\Enums\WorkOrderStatus::CX_FOLLOWUP->value,
        App\Enums\WorkOrderStatus::HOLD_FOR_CX->value
    ])
    ->whereDoesntHave('cxIssues', function($q) {
        $q->where('status', 'OPEN');
    })
    ->get();

echo "Menemukan " . $mismatchedOrders->count() . " order dengan status menggantung (Status CX_FOLLOWUP tetapi semua kendala RESOLVED).\n\n";

foreach ($mismatchedOrders as $order) {
    echo "========================================\n";
    echo "Memproses SPK: " . $order->spk_number . "\n";
    echo "Customer: " . $order->customer_name . "\n";
    echo "Status Saat Ini: " . $order->status->value . "\n";
    
    // Menentukan status selanjutnya berdasarkan riwayat kendala terakhir
    $lastIssue = $order->cxIssues()->latest()->first();
    $nextStatus = App\Enums\WorkOrderStatus::ASSESSMENT;
    
    if ($lastIssue) {
        echo "Kendala Terakhir: " . $lastIssue->category . " (Source: " . $lastIssue->source . ")\n";
        if ($lastIssue->source === 'GUDANG') {
            $nextStatus = App\Enums\WorkOrderStatus::ASSESSMENT;
        } else {
            $previousStatus = $order->previous_status;
            if ($previousStatus && $previousStatus !== App\Enums\WorkOrderStatus::CX_FOLLOWUP && $previousStatus !== App\Enums\WorkOrderStatus::HOLD_FOR_CX) {
                $nextStatus = $previousStatus;
            } else {
                $nextStatus = match ($lastIssue->source) {
                    'WORKSHOP_PREP'   => App\Enums\WorkOrderStatus::PREPARATION,
                    'WORKSHOP_SORTIR' => App\Enums\WorkOrderStatus::SORTIR,
                    'WORKSHOP_PROD'   => App\Enums\WorkOrderStatus::PRODUCTION,
                    'WORKSHOP_QC'     => App\Enums\WorkOrderStatus::QC,
                    default           => App\Enums\WorkOrderStatus::ASSESSMENT,
                };
            }
        }
    } else {
        echo "Tidak ada riwayat kendala ditemukan di database. Mengembalikan ke ASSESSMENT.\n";
    }
    
    $nextStatusVal = $nextStatus instanceof App\Enums\WorkOrderStatus ? $nextStatus->value : $nextStatus;
    echo "Tindakan: Pindahkan status utama ke -> " . $nextStatusVal . "\n";
    
    // Update status
    $order->status = $nextStatus;
    $order->save();
    
    // Buat log aktivitas
    $order->logs()->create([
        'step' => 'CX_FOLLOWUP',
        'action' => 'CX_RESOLVE_MISMATCH',
        'user_id' => 1, // System / Admin
        'description' => "Perbaikan otomatis mismatch: Status utama diperbarui ke " . $nextStatusVal . " karena kendala telah diselesaikan."
    ]);
    
    echo "Hasil: SPK " . $order->spk_number . " berhasil diperbarui ke status " . $nextStatusVal . ".\n";
}

echo "\nSemua perbaikan status selesai dijalankan!\n";
