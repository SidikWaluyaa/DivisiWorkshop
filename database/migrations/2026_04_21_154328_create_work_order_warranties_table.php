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
        Schema::create('work_order_warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->string('garansi_spk_number')->unique();
            $table->text('description'); // keluhan garansi
            $table->string('status')->default('OPEN')->index(); // OPEN, FINISHED
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('finished_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            // Additional indexes for performance on large data
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_warranties');
    }
};
