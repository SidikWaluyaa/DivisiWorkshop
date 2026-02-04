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
        // Helper to check column existence without triggering DBAL introspection (which fails on older DBs)
        $hasColumn = function($table, $column) {
            return DB::table('information_schema.columns')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', $table)
                ->where('column_name', $column)
                ->exists();
        };

        // Add item_notes to cs_quotation_items
        if (!$hasColumn('cs_quotation_items', 'item_notes')) {
            Schema::table('cs_quotation_items', function (Blueprint $table) {
                $table->text('item_notes')->nullable();
            });
        }

        // Add item_notes to cs_spk_items
        if (!$hasColumn('cs_spk_items', 'item_notes')) {
            Schema::table('cs_spk_items', function (Blueprint $table) {
                $table->text('item_notes')->nullable();
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

        if ($hasColumn('cs_quotation_items', 'item_notes')) {
            Schema::table('cs_quotation_items', function (Blueprint $table) {
                $table->dropColumn('item_notes');
            });
        }

        if ($hasColumn('cs_spk_items', 'item_notes')) {
            Schema::table('cs_spk_items', function (Blueprint $table) {
                $table->dropColumn('item_notes');
            });
        }
    }
};
