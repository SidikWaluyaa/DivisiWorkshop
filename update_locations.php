<?php

use Illuminate\Support\Facades\DB;

// Update existing orders with location based on their current status
DB::table('work_orders')->whereNull('current_location')->get()->each(function($order) {
    $location = match($order->status) {
        'DITERIMA' => 'Gudang Penerimaan',
        'ASSESSMENT' => 'Rak Sepatu',
        'PREPARATION' => 'Rumah Hijau',
        'SORTIR' => 'Rumah Hijau',
        'PRODUCTION' => 'Rumah Abu',
        'QC' => 'Rumah Abu',
        'SELESAI' => 'Rak Selesai / Pickup Area (Rumah Hijau)',
        default => null
    };
    
    if ($location) {
        DB::table('work_orders')
            ->where('id', $order->id)
            ->update(['current_location' => $location]);
    }
});

echo "Location updated for existing orders!\n";
