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
        Schema::create('algorithm_configs', function (Blueprint $table) {
            $table->id();
            $table->string('algorithm_name')->unique(); // e.g., 'auto_assignment', 'load_balancing'
            $table->boolean('is_active')->default(true);
            $table->longText('parameters'); // Flexible JSON for different algorithm parameters (stored as text for MySQL 5.6 compatibility)
            $table->text('description')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->string('status')->default('idle'); // idle, running, error
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('algorithm_configs');
    }
};
