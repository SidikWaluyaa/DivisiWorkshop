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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['before', 'after']); // Pembayaran Before (DP/Awal) or After (Pelunasan)
            
            // Amounts
            $table->integer('amount_total'); // Total Bayar (Jasa + Ongkir)
            $table->integer('amount_service')->default(0); // Porsi Jasa
            $table->integer('amount_shipping')->default(0); // Porsi Ongkir
            
            // Details
            $table->string('payment_method'); // Jenis Bayar (Cash, Transfer, dll)
            $table->timestamp('paid_at')->useCurrent(); // Tgl Bayar
            $table->foreignId('pic_id')->constrained('users'); // PIC Finance
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
