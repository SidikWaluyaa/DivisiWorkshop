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
        Schema::table('cx_issues', function (Blueprint $table) {
            // Source differentiation: GUDANG, WORKSHOP_PREP, WORKSHOP_SORTIR, WORKSHOP_PROD, WORKSHOP_QC, MANUAL
            $table->string('source')->default('GUDANG')->after('type');
        });

        // Backfill existing data: categorize based on existing `category` column
        // Records with category 'Kondisi Awal' are from Gudang QC Reject
        // All others are from Manual (Workshop form submissions)
        DB::table('cx_issues')
            ->where('category', 'Kondisi Awal')
            ->update(['source' => 'GUDANG']);

        DB::table('cx_issues')
            ->where('category', '!=', 'Kondisi Awal')
            ->update(['source' => 'MANUAL']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cx_issues', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
