<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\CxIssue;
use Carbon\Carbon;

echo "FINAL PRECISION SYNC: 1 - 4 APRIL 2026\n";
echo "======================================\n";

function syncIssue($spkPart, $targetDate, $notes, $type = 'tambah_jasa', $shouldCount = true) {
    $wo = WorkOrder::where('spk_number', 'LIKE', "%{$spkPart}%")->first();
    if (!$wo) {
        echo "FAIL: SPK *{$spkPart}* not found.\n";
        return;
    }

    $issue = CxIssue::where('work_order_id', $wo->id)->first();
    
    // If we want it to count, we MUST include keywords from the services in the notes
    // If not, we just put 'lanjut' without keywords.
    $finalType = $shouldCount ? $type : 'lanjut';
    $finalNotes = $notes;

    if (!$issue) {
        $issue = CxIssue::create([
            'work_order_id' => $wo->id,
            'description' => "Manual sync: " . $finalNotes,
            'status' => 'RESOLVED',
            'resolved_at' => Carbon::parse($targetDate)->setHour(12),
            'resolution_type' => $finalType,
            'resolution_notes' => $finalNotes,
            'user_id' => 1,
            'resolved_by' => 1
        ]);
        echo "CREATED: Tiket baru {$wo->spk_number} | " . ($shouldCount ? 'UPSELL' : 'LANJUT') . "\n";
    } else {
        $issue->update([
            'status' => 'RESOLVED',
            'resolved_at' => Carbon::parse($targetDate)->setHour(12),
            'resolution_type' => $finalType,
            'resolution_notes' => $finalNotes
        ]);
        echo "UPDATED: {$wo->spk_number} -> Tanggal {$targetDate} | " . ($shouldCount ? 'UPSELL' : 'LANJUT') . "\n";
    }
}

// --- TANGGAL 1 APRIL (TARGET: 965k / 6 SPK) ---
syncIssue('0314-AW', '2026-04-01', 'Ganti lance guard lapis kulit', 'tambah_jasa', true);  // 225k
syncIssue('0479-AW', '2026-04-01', 'upper treatment', 'tambah_jasa', true);                // 90k
syncIssue('0017-MY', '2026-04-01', 'ganti lining insole kulit', 'tambah_jasa', true);      // 175k
syncIssue('0633-QA', '2026-04-01', 'ganti stripe', 'tambah_jasa', true);                   // 175k
syncIssue('0307-QA', '2026-04-01', 'ganti alas jeruk', 'tambah_jasa', true);               // 225k
syncIssue('0036-RQ', '2026-04-01', 'Lapkul bagian yg sobek', 'tambah_jasa', true);         // 75k
// Sisanya dr tanggal 1 set ke 'lanjut' agar tidak mengacaukan nominal
syncIssue('0587-AW', '2026-04-01', 'Lanjut saja', 'lanjut', false);
syncIssue('0264-MY', '2026-04-01', 'Lanjut saja', 'lanjut', false);
syncIssue('0300-RQ', '2026-04-01', 'Lanjut saja', 'lanjut', false);
syncIssue('0197-VN', '2026-04-01', 'Lanjut saja', 'lanjut', false);
syncIssue('0819-AW', '2026-04-01', 'Lanjut saja', 'lanjut', false);
syncIssue('0436-VN', '2026-04-01', 'Lanjut saja', 'lanjut', false);
syncIssue('0305-QA', '2026-04-01', 'Lanjut saja', 'lanjut', false);
syncIssue('0306-QA', '2026-04-01', 'Lanjut saja', 'lanjut', false);

// --- TANGGAL 2 APRIL (TARGET: 635k / 3 SPK) ---
syncIssue('0145-VN', '2026-04-02', 'Lapis kulit keliling', 'tambah_jasa', true);           // 125k
syncIssue('0101-AW', '2026-04-02', 'ganti heel cage', 'tambah_jasa', true);                // 175k
syncIssue('0394-AW', '2026-04-02', 'Midsole eva warna putih', 'tambah_jasa', true);       // 335k

// --- TANGGAL 3 APRIL (TARGET: 1.180k / 6 SPK) ---
syncIssue('0682-RQ', '2026-04-03', 'Repaint Spesial', 'tambah_jasa', true);                // 175k
syncIssue('0692-VN', '2026-04-03', 'Rapikan jahitan', 'tambah_jasa', true);                // 30k
syncIssue('0502-AW', '2026-04-03', 'Ganti bemper', 'tambah_jasa', true);                   // 225k
syncIssue('0900-RQ', '2026-04-03', 'Repaint spesial', 'tambah_jasa', true);                // 175k
syncIssue('0781-VN', '2026-04-03', 'lining + heel tab', 'tambah_jasa', true);             // 300k
syncIssue('0889-RQ', '2026-04-03', 'Ganti lace guard', 'tambah_jasa', true);               // 275k

// --- TANGGAL 4 APRIL (TARGET: 975k / 6 SPK) ---
syncIssue('0941-IK', '2026-04-04', 'ganti upper + DTF', 'tambah_jasa', true);              // 250k
syncIssue('0331-QA', '2026-04-04', 'ganti lining', 'tambah_jasa', true);                   // 175k
syncIssue('0932-RQ', '2026-04-04', 'ganti bemper', 'tambah_jasa', true);                   // 175k
syncIssue('0606-AW', '2026-04-04', 'ganti heel cage', 'tambah_jasa', true);                // 175k
syncIssue('0653-SB', '2026-04-04', 'lem checkup', 'tambah_jasa', true);                    // 75k
syncIssue('0909-AW', '2026-04-04', 'midsole + lapkul', 'tambah_jasa', true);               // 125k

echo "\nSINKRONISASI AKHIR SELESAI!\n";
echo "Summary Target:\n";
echo "1 April: 6 SPK / Rp 965.000\n";
echo "2 April: 3 SPK / Rp 635.000\n";
echo "3 April: 6 SPK / Rp 1.180.000\n";
echo "4 April: 6 SPK / Rp 975.000\n";
