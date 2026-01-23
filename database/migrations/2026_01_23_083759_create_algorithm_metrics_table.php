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
        Schema::create('algorithm_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('algorithm_name');
            $table->string('metric_name'); // e.g., 'assignment_success_rate', 'avg_queue_length'
            $table->decimal('value', 10, 2);
            $table->string('unit')->nullable(); // %, count, seconds, etc.
            $table->timestamp('recorded_at');
            $table->longText('metadata')->nullable(); // Additional context (stored as text for MySQL 5.6 compatibility)
            $table->timestamps();
            
            $table->index(['algorithm_name', 'metric_name', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('algorithm_metrics');
    }
};
