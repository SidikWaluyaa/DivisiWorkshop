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
        // Helper to check column existence safely
        $hasColumn = function($table, $column) {
            return DB::table('information_schema.columns')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', $table)
                ->where('column_name', $column)
                ->exists();
        };

        if (!$hasColumn('cs_leads', 'channel')) {
            Schema::table('cs_leads', function (Blueprint $table) {
                // Default ONLINE (WhatsApp/IG)
                $table->enum('channel', ['ONLINE', 'OFFLINE'])->default('ONLINE')->after('customer_name');
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

        if ($hasColumn('cs_leads', 'channel')) {
            Schema::table('cs_leads', function (Blueprint $table) {
                $table->dropColumn('channel');
            });
        }
    }
};
