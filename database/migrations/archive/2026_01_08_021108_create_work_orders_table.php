<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('spk_number')->unique();
            
            // Customer Data
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_address')->nullable();
            
            // Product Info
            $table->string('shoe_brand')->nullable();
            $table->string('shoe_size')->nullable();
            $table->string('shoe_color')->nullable();
            
            // Process Status
            // Enums matching the flow: DITERIMA, CUCI, ASSESSMENT, PREPARATION, SORTIR, PRODUCTION, QC, SELESAI
            $table->string('status')->default('DITERIMA'); 
            $table->string('location')->default('Rak Penerimaan');
            
            // Timestamps
            $table->dateTime('entry_date');
            $table->dateTime('estimation_date');
            $table->dateTime('finished_date')->nullable();
            $table->dateTime('taken_date')->nullable(); // Pickup
            
            $table->string('priority')->default('Normal'); // Normal, Express

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
