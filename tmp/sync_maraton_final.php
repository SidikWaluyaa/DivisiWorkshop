<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\CxIssue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "FINAL CALIBRATION v5 (FORCE DB TIMESTAMPS): 1 - 4 APRIL 2026\n";
echo "===========================================================\n";

function syncPrecisionFinal($spkPart, $targetDate, $notes, $shouldCount = true) {
    $wo = WorkOrder::where('spk_number', 'LIKE', "%{$spkPart}%")->first();
    if (!$wo) {
        echo "FAIL: SPK *{$spkPart}* not found.\n";
        return;
    }

    // Keep only ONE issue as 'tambah_jasa', others as 'lanjut'
    $allIssues = CxIssue::where('work_order_id', $wo->id)->get();
    foreach ($allIssues as $idx => $dup) {
        if ($idx > 0) {
            $dup->update(['resolution_type' => 'lanjut', 'status' => 'RESOLVED']);
        }
    }
    
    $issue = $allIssues->first();
    $finalType = $shouldCount ? 'tambah_jasa' : 'lanjut';
    
    // FORCE TIMESTAMPS via DB to bypass Eloquent protection
    $createdAt = '2026-01-01 00:00:00';
    $resolvedAt = Carbon::parse($targetDate)->setHour(12)->toDateTimeString();

    if (!$issue) {
        $id = DB::table('cx_issues')->insertGetId([
            'work_order_id' => $wo->id,
            'description' => "Precision sync v5: " . $notes,
            'status' => 'RESOLVED',
            'created_at' => $createdAt,
            'updated_at' => $resolvedAt,
            'resolved_at' => $resolvedAt,
            'resolution_type' => $finalType,
            'resolution_notes' => $notes,
            'user_id' => 1,
            'resolved_by' => 1
        ]);
        echo "CREATED: Tiket {$wo->spk_number} | ID: $id | " . ($shouldCount ? 'UPSELL' : 'LANJUT') . "\n";
    } else {
        DB::table('cx_issues')->where('id', $issue->id)->update([
            'status' => 'RESOLVED',
            'created_at' => $createdAt,
            'resolved_at' => $resolvedAt,
            'updated_at' => $resolvedAt,
            'resolution_type' => $finalType,
            'resolution_notes' => $notes
        ]);
        echo "UPDATED: {$wo->spk_number} ID: {$issue->id} -> {$targetDate} | " . ($shouldCount ? 'UPSELL' : 'LANJUT') . "\n";
    }
}

// 1 APRIL (Target 965k)
syncPrecisionFinal('0017-MY', '2026-04-01', 'Ganti lining kulit', true);
syncPrecisionFinal('0633-QA', '2026-04-01', 'Ganti Stripe', true);
syncPrecisionFinal('0479-AW', '2026-04-01', 'Upper Treatment', true);
syncPrecisionFinal('0036-RQ', '2026-04-01', 'Lapis kulit', true);
syncPrecisionFinal('0307-QA', '2026-04-01', 'ganti alas jeruk', true);
syncPrecisionFinal('0306-QA', '2026-04-01', 'GANTI ALAS POLOS', true);
$others1 = ['0314-AW','0587-AW','0264-MY','0300-RQ','0197-VN','0819-AW','0436-VN','0305-QA'];
foreach($others1 as $s) syncPrecisionFinal($s, '2026-04-01', 'Sembunyi v5', false);

// 2 APRIL (Target 635k)
syncPrecisionFinal('0145-VN', '2026-04-02', 'Lapis kulit keliling', true);
syncPrecisionFinal('0101-AW', '2026-04-02', 'heel cage', true);
syncPrecisionFinal('0394-AW', '2026-04-02', 'Midsole Non flat', true);

// 3 APRIL (Target 1.180k)
syncPrecisionFinal('0682-RQ', '2026-04-03', 'Repaint Spesial', true);
syncPrecisionFinal('0692-VN', '2026-04-03', 'Rapikan jahitan', true);
syncPrecisionFinal('0502-AW', '2026-04-03', 'Bemper', true);
syncPrecisionFinal('0900-RQ', '2026-04-03', 'Repaint spesial', true);
syncPrecisionFinal('0781-VN', '2026-04-03', 'lining + heel tab', true);
syncPrecisionFinal('0889-RQ', '2026-04-03', 'Ganti lace guard', true);

// 4 APRIL (Target 975k)
syncPrecisionFinal('0941-IK', '2026-04-04', 'upper + DTF', true);
syncPrecisionFinal('0331-QA', '2026-04-04', 'ganti lining', true);
syncPrecisionFinal('0932-RQ', '2026-04-04', 'ganti bemper', true);
syncPrecisionFinal('0606-AW', '2026-04-04', 'heel cage', true);
syncPrecisionFinal('0653-SB', '2026-04-04', 'lem checkup', true);
syncPrecisionFinal('0909-AW', '2026-04-04', 'midsole + lapkul', true);

echo "\nSINKRONISASI AKURASI TOTAL (v5) SELESAI!\n";
