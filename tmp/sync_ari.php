<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CxIssue;

echo "DATA SYNC: ARI (435)\n";
echo "====================\n";

$i = CxIssue::find(435);
if (!$i) {
    echo "ID 435 NOT FOUND!\n";
    exit;
}

$oldNotes = $i->resolution_notes;
$newNotes = "midsole + lapkul (" . $oldNotes . ")";

$i->update([
    'resolution_notes' => $newNotes
]);

echo "BERHASIL DIUPDATE!\n";
echo "Lama: '{$oldNotes}'\n";
echo "Baru: '{$newNotes}'\n";
echo "====================\n";
echo "SILAKAN CEK: php artisan cx:audit-precision 2026-04-04\n";
echo "SALDO NOMINAL TARGET: Rp 975.000\n";
