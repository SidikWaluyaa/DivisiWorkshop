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
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('status')->default('PENDING'); // PENDING, ON_PROGRESS, DONE
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_services');
    }
};
