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
        Schema::create('storage_racks', function (Blueprint $table) {
            $table->id();
            $table->string('rack_code')->unique(); // A1, A2, B1, etc.
            $table->string('location')->nullable(); // Lantai 1, Area A, etc.
            $table->integer('capacity')->default(20); // Max items per rack
            $table->integer('current_count')->default(0); // Current items stored
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('rack_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_racks');
    }
};
