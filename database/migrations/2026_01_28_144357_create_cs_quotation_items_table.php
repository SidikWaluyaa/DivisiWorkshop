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
        Schema::create('cs_quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('cs_quotations')->onDelete('cascade');
            $table->integer('item_number')->default(1); // Item #1, #2, #3...
            
            // Item Data
            $table->string('category')->nullable(); // Sepatu, Tas, Dompet, Topi, Lainnya
            $table->string('shoe_type')->nullable(); // Casual, Sneakers, Outdoor, Sport
            $table->string('shoe_brand')->nullable(); // Nike, Adidas, Puma, New Balance
            $table->string('shoe_size')->nullable(); // 40, 41, 42, 43
            $table->string('shoe_color')->nullable(); // Merah, Hitam Putih
            
            // Photo & Condition
            $table->string('photo_path')->nullable();
            $table->text('condition_notes')->nullable();
            
            // Services (will be filled during SPK generation)
            $table->longText('services')->nullable();
            $table->decimal('item_total_price', 12, 2)->default(0);
            
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['quotation_id', 'item_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cs_quotation_items');
    }
};
