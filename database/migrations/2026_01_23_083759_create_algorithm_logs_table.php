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
        Schema::create('algorithm_logs', function (Blueprint $table) {
            $table->id();
            $table->string('algorithm_name'); // Which algorithm performed this action
            $table->string('action_type'); // e.g., 'auto_assign', 'priority_update', 'load_balance'
            $table->foreignId('work_order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Affected user (technician)
            $table->json('metadata')->nullable(); // Additional context (old_value, new_value, reason, etc.)
            $table->string('result')->default('success'); // success, failed, partial
            $table->text('error_message')->nullable();
            $table->decimal('execution_time_ms', 10, 2)->nullable(); // Performance tracking
            $table->timestamps();
            
            $table->index(['algorithm_name', 'created_at']);
            $table->index('work_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('algorithm_logs');
    }
};
