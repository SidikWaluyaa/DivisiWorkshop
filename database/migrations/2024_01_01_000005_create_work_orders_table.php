<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('spk_number')->unique();
            
            // Customer Info
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address')->nullable();
            
            // Shoe Details
            $table->string('shoe_brand')->nullable();
            $table->string('shoe_type')->nullable();
            $table->string('shoe_size')->nullable();
            $table->string('shoe_color')->nullable();
            
            // Status & Tracking
            $table->string('status')->default('DITERIMA');
            $table->text('notes')->nullable();
            $table->string('priority')->default('Normal');
            $table->string('current_location')->nullable();
            $table->boolean('is_revising')->default(false);
            
            // Dates
            $table->timestamp('entry_date')->useCurrent();
            $table->timestamp('taken_date')->nullable();
            $table->timestamp('estimation_date')->nullable();
            $table->timestamp('finished_date')->nullable();
            
            // === PREPARATION PHASE ===
            // Washing
            $table->timestamp('prep_washing_started_at')->nullable();
            $table->timestamp('prep_washing_completed_at')->nullable();
            $table->foreignId('prep_washing_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Sol Preparation
            $table->timestamp('prep_sol_started_at')->nullable();
            $table->timestamp('prep_sol_completed_at')->nullable();
            $table->foreignId('prep_sol_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Upper Preparation
            $table->timestamp('prep_upper_started_at')->nullable();
            $table->timestamp('prep_upper_completed_at')->nullable();
            $table->foreignId('prep_upper_by')->nullable()->constrained('users')->nullOnDelete();
            
            // === PRODUCTION PHASE ===
            // Sol Production
            $table->timestamp('prod_sol_started_at')->nullable();
            $table->timestamp('prod_sol_completed_at')->nullable();
            $table->foreignId('prod_sol_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Upper Production (Repaint/Upper)
            $table->timestamp('prod_upper_started_at')->nullable();
            $table->timestamp('prod_upper_completed_at')->nullable();
            $table->foreignId('prod_upper_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Cleaning Production
            $table->timestamp('prod_cleaning_started_at')->nullable();
            $table->timestamp('prod_cleaning_completed_at')->nullable();
            $table->foreignId('prod_cleaning_by')->nullable()->constrained('users')->nullOnDelete();
            
            // === QC PHASE ===
            // QC Jahit
            $table->timestamp('qc_jahit_started_at')->nullable();
            $table->timestamp('qc_jahit_completed_at')->nullable();
            $table->foreignId('qc_jahit_by')->nullable()->constrained('users')->nullOnDelete();
            
            // QC Cleanup
            $table->timestamp('qc_cleanup_started_at')->nullable();
            $table->timestamp('qc_cleanup_completed_at')->nullable();
            $table->foreignId('qc_cleanup_by')->nullable()->constrained('users')->nullOnDelete();
            
            // QC Final
            $table->timestamp('qc_final_started_at')->nullable();
            $table->timestamp('qc_final_completed_at')->nullable();
            $table->foreignId('qc_final_by')->nullable()->constrained('users')->nullOnDelete();
            
            // === LEGACY ASSIGNMENT COLUMNS ===
            // (Kept for backward compatibility with existing controllers)
            $table->foreignId('technician_production_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pic_sortir_sol_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pic_sortir_upper_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('qc_jahit_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('qc_cleanup_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('qc_final_pic_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
