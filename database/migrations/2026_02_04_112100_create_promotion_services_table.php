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
        Schema::create('promotion_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotion_id');
            $table->unsignedBigInteger('service_id');
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('promotion_id')
                  ->references('id')
                  ->on('promotions')
                  ->onDelete('cascade');
                  
            $table->foreign('service_id')
                  ->references('id')
                  ->on('services')
                  ->onDelete('cascade');
            
            // Unique Constraint
            $table->unique(['promotion_id', 'service_id'], 'unique_promo_service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_services');
    }
};
