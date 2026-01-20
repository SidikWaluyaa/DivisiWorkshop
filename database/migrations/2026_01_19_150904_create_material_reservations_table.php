<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->foreignId('oto_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('work_order_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->integer('quantity');
            $table->enum('type', ['SOFT', 'HARD'])->default('SOFT'); // Soft = temporary, Hard = confirmed
            $table->enum('status', ['ACTIVE', 'CONFIRMED', 'RELEASED', 'EXPIRED'])->default('ACTIVE');
            
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('released_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('material_id');
            $table->index('oto_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_reservations');
    }
};
