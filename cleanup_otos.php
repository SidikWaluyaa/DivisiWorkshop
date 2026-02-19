<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OTO;
use Illuminate\Support\Facades\DB;

$affected = DB::table('otos')
    ->whereIn('status', ['CANCELLED', 'REJECTED'])
    ->orWhereNotNull('deleted_at')
    ->delete();

echo "Successfully purged $affected OTO records from the database.\n";
