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
        Schema::create('cs_spk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained('cs_spk')->onDelete('cascade');
            $table->foreignId('quotation_item_id')->nullable()->constrained('cs_quotation_items')->onDelete('set null');
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->onDelete('set null');
            
            // Copy of item data (for immutability/history)
            $table->string('category')->nullable();
            $table->string('shoe_type')->nullable();
            $table->string('shoe_brand')->nullable();
            $table->string('shoe_size')->nullable();
            $table->string('shoe_color')->nullable();
            
            // Services assigned to this item
            $table->json('services');
            $table->decimal('item_total_price', 12, 2)->default(0);
            
            // Status tracking per item
            $table->enum('status', [
                'PENDING',
                'HANDED_TO_WORKSHOP',
                'IN_PROGRESS',
                'COMPLETED'
            ])->default('PENDING');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['spk_id', 'status']);
            $table->index('work_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cs_spk_items');
    }
};
