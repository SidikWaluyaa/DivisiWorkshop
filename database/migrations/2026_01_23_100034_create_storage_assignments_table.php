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
        Schema::create('storage_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->string('rack_code');
            $table->timestamp('stored_at');
            $table->timestamp('retrieved_at')->nullable();
            $table->foreignId('stored_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('retrieved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['stored', 'retrieved'])->default('stored');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('work_order_id');
            $table->index('rack_code');
            $table->index('status');
            $table->index('stored_at');
            
            $table->foreign('rack_code')->references('rack_code')->on('storage_racks')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_assignments');
    }
};
