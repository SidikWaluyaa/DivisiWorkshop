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
        Schema::create('promotion_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotion_id');
            
            // Related Entities
            $table->unsignedBigInteger('cs_lead_id')->nullable();
            $table->unsignedBigInteger('cs_spk_id')->nullable();
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->string('customer_phone', 20)->nullable();
            
            // Pricing Details
            $table->decimal('original_amount', 12, 2)->comment('Price before discount');
            $table->decimal('discount_amount', 12, 2)->comment('Discount amount');
            $table->decimal('final_amount', 12, 2)->comment('Price after discount');
            
            // Audit
            $table->unsignedBigInteger('applied_by')->nullable()->comment('User ID who applied the promo');
            $table->timestamp('applied_at')->useCurrent();
            
            // Foreign Keys
            $table->foreign('promotion_id')
                  ->references('id')
                  ->on('promotions')
                  ->onDelete('cascade');
                  
            $table->foreign('cs_lead_id')
                  ->references('id')
                  ->on('cs_leads')
                  ->onDelete('set null');
                  
            // Note: cs_spks table doesn't exist yet, so we skip this FK for now
            // Will be added later when cs_spks table is created
                  
            $table->foreign('work_order_id')
                  ->references('id')
                  ->on('work_orders')
                  ->onDelete('set null');
            
            // Indexes
            $table->index('customer_phone');
            $table->index('cs_spk_id');
            $table->index('applied_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_usage_logs');
    }
};
