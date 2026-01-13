<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->decimal('cost', 15, 2)->default(0); // Snapshotted cost at time of order
            $table->string('status')->default('PENDING');
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete(); // Added for multi-tech assignment
            $table->timestamps();
        });

        Schema::create('work_order_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->string('status')->default('PENDING'); // Added to match query requirements
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_services');
        Schema::dropIfExists('work_order_materials');
    }
};
