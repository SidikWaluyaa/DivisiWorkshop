<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OTO;
use Carbon\Carbon;

echo "🕵️‍♂️ PENGECEKAN DATA OTO (APRIL 2026)\n";
echo "====================================\n";

$start = Carbon::parse('2026-04-01')->startOfMonth();
$end = Carbon::parse('2026-04-30')->endOfMonth();

$allOtos = OTO::whereBetween('created_at', [$start, $end])
    ->orWhereBetween('customer_responded_at', [$start, $end])
    ->with('workOrder')
    ->get();

if ($allOtos->isEmpty()) {
    echo "❌ Tidak ditemukan data OTO sama sekali di bulan April 2026.\n";
} else {
    echo "Ditemukan " . $allOtos->count() . " data OTO:\n\n";
    foreach ($allOtos as $oto) {
        echo sprintf("[%s] SPK: %s | Status: %s | Respon: %s | CX Assign: %s | Price: %s\n",
            $oto->created_at->format('d/m'),
            $oto->workOrder->spk_number ?? 'N/A',
            $oto->status,
            $oto->customer_responded_at ?: 'Belum Respon',
            $oto->cx_assigned_to ?: 'NULL',
            $oto->total_oto_price ?: '0'
        );
    }
}

echo "\n------------------------------------\n";
echo "LOGIKA FILTER DASHBOARD SAAT INI:\n";
echo "- Status: ACCEPTED\n";
echo "- Tanggal: Berdasar 'customer_responded_at'\n";
echo "- Syarat CX: cx_assigned_to != NULL OR cx_contacted_at != NULL\n";
