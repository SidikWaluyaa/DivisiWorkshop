<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\CxIssue;
use Carbon\Carbon;

echo "FINAL MASS SYNC: 1 - 3 APRIL 2026\n";
echo "==================================\n";

// Helper function to sync issue
function syncIssue($spkPart, $targetDate, $notes, $type = 'tambah_jasa') {
    $wo = WorkOrder::where('spk_number', 'LIKE', "%{$spkPart}%")->first();
    if (!$wo) {
        echo "FAIL: SPK *{$spkPart}* not found.\n";
        return;
    }

    $issue = CxIssue::where('work_order_id', $wo->id)->first();
    
    if (!$issue) {
        // Create new issue if missing
        $issue = CxIssue::create([
            'work_order_id' => $wo->id,
            'description' => "Manual sync: " . $notes,
            'status' => 'RESOLVED',
            'resolved_at' => Carbon::parse($targetDate)->setHour(12),
            'resolution_type' => $type,
            'resolution_notes' => $notes,
            'user_id' => 1,
            'resolved_by' => 1
        ]);
        echo "CREATED: Tiket CX baru untuk {$wo->spk_number} | Target: {$targetDate}\n";
    } else {
        // Update existing
        $issue->update([
            'status' => 'RESOLVED',
            'resolved_at' => Carbon::parse($targetDate)->setHour(12), // Force to target date
            'resolution_type' => $type,
            'resolution_notes' => $notes . " (Synced)"
        ]);
        echo "UPDATED: Tiket CX ID {$issue->id} ({$wo->spk_number}) -> Tanggal: {$targetDate}\n";
    }
}

// --- DATA SINKRONISASI 1 APRIL ---
syncIssue('0314-AW', '2026-04-01', 'Ganti lance guard lapis kulit'); // Amin
syncIssue('0587-AW', '2026-04-01', 'Lapis kulit keliling'); // tazkil
syncIssue('0264-MY', '2026-04-01', 'ganti bemper lapis kulit'); // kendri
syncIssue('0036-RQ', '2026-04-01', 'Lapkul bagian yg sobek'); // Abi
syncIssue('0479-AW', '2026-04-01', 'upper treatment'); // Asyer
syncIssue('0300-RQ', '2026-04-01', 'ganti upper pola standar'); // Tata
syncIssue('0197-VN', '2026-04-01', 'ganti bemper bahan kulit'); // Leonardo
syncIssue('0819-AW', '2026-04-01', 'tambal jahitan'); // Edi
// Julia, Taufiq, Akbar Salim, Emerson are already roughly correct or have existing tickets.
// Fix Akbar Salim 0305 notes
syncIssue('0305-QA', '2026-04-01', 'ganti alas LNP'); 

// --- DATA SINKRONISASI 2 APRIL ---
syncIssue('0394-AW', '2026-04-02', 'Midsole eva warna putih'); // Ryan

echo "\nSINKRONISASI MASSAL SELESAI!\n";
echo "Silakan jalankan: php artisan cx:audit-precision 2026-04-01\n";
echo "Lalu cek tanggal 2 dan 3 juga.\n";
