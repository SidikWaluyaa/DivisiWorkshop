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
        Schema::create('work_order_additional_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->string('spk_number')->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('service_name');
            $table->string('category_name')->default('Custom');
            $table->decimal('cost', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('IN_PROGRESS')->index();

            // Technician Tracking (modeled after OTO station)
            $table->foreignId('sol_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sol_started_at')->nullable();
            $table->timestamp('sol_completed_at')->nullable();

            $table->foreignId('upper_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('upper_started_at')->nullable();
            $table->timestamp('upper_completed_at')->nullable();

            $table->foreignId('treatment_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('treatment_started_at')->nullable();
            $table->timestamp('treatment_completed_at')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_additional_services');
    }
};
