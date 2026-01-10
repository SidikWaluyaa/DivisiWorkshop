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
            // Production - Sol
            $table->timestamp('prod_sol_started_at')->nullable();
            $table->timestamp('prod_sol_completed_at')->nullable();
            $table->foreignId('prod_sol_by')->nullable()->constrained('users');

            // Production - Upper (Repaint/Upper)
            $table->timestamp('prod_upper_started_at')->nullable();
            $table->timestamp('prod_upper_completed_at')->nullable();
            $table->foreignId('prod_upper_by')->nullable()->constrained('users');

            // Production - Cleaning
            $table->timestamp('prod_cleaning_started_at')->nullable();
            $table->timestamp('prod_cleaning_completed_at')->nullable();
            $table->foreignId('prod_cleaning_by')->nullable()->constrained('users');

            // QC - Jahit
            $table->timestamp('qc_jahit_started_at')->nullable();
            $table->timestamp('qc_jahit_completed_at')->nullable();
            $table->foreignId('qc_jahit_by')->nullable()->constrained('users');

            // QC - Cleanup
            $table->timestamp('qc_cleanup_started_at')->nullable();
            $table->timestamp('qc_cleanup_completed_at')->nullable();
            $table->foreignId('qc_cleanup_by')->nullable()->constrained('users');

            // QC - Final
            $table->timestamp('qc_final_started_at')->nullable();
            $table->timestamp('qc_final_completed_at')->nullable();
            $table->foreignId('qc_final_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn([
                'prod_sol_started_at', 'prod_sol_completed_at', 'prod_sol_by',
                'prod_upper_started_at', 'prod_upper_completed_at', 'prod_upper_by',
                'prod_cleaning_started_at', 'prod_cleaning_completed_at', 'prod_cleaning_by',
                'qc_jahit_started_at', 'qc_jahit_completed_at', 'qc_jahit_by',
                'qc_cleanup_started_at', 'qc_cleanup_completed_at', 'qc_cleanup_by',
                'qc_final_started_at', 'qc_final_completed_at', 'qc_final_by',
            ]);
        });
    }
};
