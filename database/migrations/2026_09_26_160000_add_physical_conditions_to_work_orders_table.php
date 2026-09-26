<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('work_orders', 'desc_upper')) {
                $table->text('desc_upper')->nullable()->after('warehouse_qc_notes');
            }
            if (!Schema::hasColumn('work_orders', 'desc_sol')) {
                $table->text('desc_sol')->nullable()->after('desc_upper');
            }
            if (!Schema::hasColumn('work_orders', 'desc_kondisi_bawaan')) {
                $table->text('desc_kondisi_bawaan')->nullable()->after('desc_sol');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['desc_upper', 'desc_sol', 'desc_kondisi_bawaan']);
        });
    }
};
