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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_masuk');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('spk_number');
            $table->boolean('is_verified')->default(false);
            $table->date('tanggal_pengiriman')->nullable();
            $table->string('pic')->nullable();
            $table->string('resi_pengiriman')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
