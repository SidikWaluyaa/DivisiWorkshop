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
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address')->nullable();
            
            // Shoe Details
            $table->string('shoe_brand')->nullable();
            $table->string('shoe_type')->nullable();
            $table->string('shoe_size')->nullable(); // Added to match import
            $table->string('shoe_color')->nullable();
            
            // Status & Tracking
            $table->string('status')->default('DITERIMA'); // DITERIMA, PREPARATION, SORTIR, PRODUCTION, QC, SELESAI
            $table->string('priority')->default('Normal'); // Normal, Urgent
            $table->string('current_location')->nullable();
            
            // Dates
            $table->timestamp('entry_date')->useCurrent();
            $table->timestamp('taken_date')->nullable(); // Added to match query
            $table->timestamp('estimation_date')->nullable();
            $table->timestamp('finished_date')->nullable();
            
            // Technician Assignments (FKs)
            $table->foreignId('technician_production_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pic_sortir_sol_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pic_sortir_upper_id')->nullable()->constrained('users')->nullOnDelete();
            
            // QC Technicians
            $table->foreignId('qc_jahit_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('qc_cleanup_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('qc_final_pic_id')->nullable()->constrained('users')->nullOnDelete();
            // Note: QC and Washing technicians might be logged via Logs or additional columns if needed, 
            // but simplified here based on observed usage.
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Added to match import
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
