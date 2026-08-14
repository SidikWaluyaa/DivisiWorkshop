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
            return DB::table('information_schema.columns')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', $table)
                ->where('column_name', $column)
                ->exists();
        };

        if (!$hasColumn('work_orders', 'suggested_services')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->text('suggested_services')->nullable()->after('warehouse_qc_notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasColumn = function($table, $column) {
            return DB::table('information_schema.columns')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', $table)
                ->where('column_name', $column)
                ->exists();
        };

        if ($hasColumn('work_orders', 'suggested_services')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->dropColumn('suggested_services');
            });
        }
    }
};
