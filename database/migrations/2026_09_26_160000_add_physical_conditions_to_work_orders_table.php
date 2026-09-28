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
        $hasColumn = function($table, $column) {
            return !empty(DB::select("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'"));
        };

        Schema::table('work_orders', function (Blueprint $table) use ($hasColumn) {
            if (!$hasColumn('work_orders', 'desc_upper')) {
                $table->text('desc_upper')->nullable()->after('warehouse_qc_notes');
            }
            if (!$hasColumn('work_orders', 'desc_sol')) {
                $table->text('desc_sol')->nullable()->after('desc_upper');
            }
            if (!$hasColumn('work_orders', 'desc_kondisi_bawaan')) {
                $table->text('desc_kondisi_bawaan')->nullable()->after('desc_sol');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasColumn = function($table, $column) {
            return !empty(DB::select("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'"));
        };

        $columnsToDrop = [];
        if ($hasColumn('work_orders', 'desc_upper')) {
            $columnsToDrop[] = 'desc_upper';
        }
        if ($hasColumn('work_orders', 'desc_sol')) {
            $columnsToDrop[] = 'desc_sol';
        }
        if ($hasColumn('work_orders', 'desc_kondisi_bawaan')) {
            $columnsToDrop[] = 'desc_kondisi_bawaan';
        }

        if (!empty($columnsToDrop)) {
            Schema::table('work_orders', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }
};
