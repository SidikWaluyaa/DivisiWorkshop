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
        Schema::create('cx_after_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->dateTime('entered_at');
            $table->dateTime('contacted_at')->nullable();
            $table->foreignId('pic_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('response', ['Puas', 'Komplain', 'Kurang Puas', 'No Respon 1x24 Jam', 'Hold'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cx_after_confirmations');
    }
};
