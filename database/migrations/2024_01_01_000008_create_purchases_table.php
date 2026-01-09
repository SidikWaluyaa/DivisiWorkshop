<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->string('supplier_name')->nullable();
            
            $table->foreignId('material_id')->constrained()->cascadeOnDelete(); // Or restrict? Cascade safer for dev.
            
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            
            $table->string('status')->default('pending'); // pending, received, cancelled
            $table->string('payment_status')->default('unpaid'); // unpaid, partial, paid
            
            $table->integer('quality_rating')->nullable()->comment('1-5 rating');
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamp('order_date')->useCurrent();
            $table->timestamp('received_date')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
