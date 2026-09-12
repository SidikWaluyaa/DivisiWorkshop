<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $hasColumn = !empty(DB::select("SHOW COLUMNS FROM `work_orders` LIKE 'unneeded_stations'"));
        if (!$hasColumn) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->json('unneeded_stations')->nullable()->after('accessories_data');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasColumn = !empty(DB::select("SHOW COLUMNS FROM `work_orders` LIKE 'unneeded_stations'"));
        if ($hasColumn) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->dropColumn('unneeded_stations');
            });
        }
    }
};
