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
            // Sortir Stage PICs
            $table->foreignId('pic_sortir_sol_id')->nullable()->constrained('users');
            $table->foreignId('pic_sortir_upper_id')->nullable()->constrained('users');

            // Production Technician
            $table->foreignId('technician_production_id')->nullable()->constrained('users');

            // QC Stage Technicians/PICs
            $table->foreignId('qc_jahit_technician_id')->nullable()->constrained('users');
            $table->foreignId('qc_cleanup_technician_id')->nullable()->constrained('users');
            $table->foreignId('qc_final_pic_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['pic_sortir_sol_id']);
            $table->dropForeign(['pic_sortir_upper_id']);
            $table->dropForeign(['technician_production_id']);
            $table->dropForeign(['qc_jahit_technician_id']);
            $table->dropForeign(['qc_cleanup_technician_id']);
            $table->dropForeign(['qc_final_pic_id']);
            
            $table->dropColumn([
                'pic_sortir_sol_id', 
                'pic_sortir_upper_id', 
                'technician_production_id', 
                'qc_jahit_technician_id', 
                'qc_cleanup_technician_id', 
                'qc_final_pic_id'
            ]);
        });
    }
};
