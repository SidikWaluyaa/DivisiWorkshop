<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkOrder;
use App\Models\CxIssue;

$missingSpks = [
    '0314-RQ', // Amin
    '0587-AW', // tazkil
    '0264-MY', // kendri
    '0036-RQ', // Abi
    '0479-AW', // Asyer
    '0300-RQ', // Tata
    '0197-VN', // Leonardo
    '0819-AW', // Edi
    '0394-AW', // Ryan
];

echo "MELACAK SPK YANG HILANG:\n";
echo "========================\n";

foreach ($missingSpks as $spkPart) {
    echo "\nMencari: *{$spkPart}*\n";
    $wo = WorkOrder::where('spk_number', 'LIKE', "%{$spkPart}%")->first();
    
    if ($wo) {
        echo "Ditemukan SPK: {$wo->spk_number}\n";
        $statusStr = $wo->status instanceof \BackedEnum ? $wo->status->value : $wo->status;
        echo "Status WO saat ini: {$statusStr}\n";
        
        $issue = CxIssue::where('work_order_id', $wo->id)->first();
        if ($issue) {
            echo "Ditemukan Tiket CX ID: {$issue->id}\n";
            echo "Status Tiket: {$issue->status}\n";
            echo "Waktu Tiket: {$issue->created_at} | Resolved: {$issue->resolved_at}\n";
        } else {
            echo "TIDAK ADA TIKET CX TERKAIT!\n";
        }
    } else {
        echo "TIDAK DITEMUKAN DI TABEL WORK_ORDERS!\n";
    }
}
