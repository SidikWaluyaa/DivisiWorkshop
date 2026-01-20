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
        Schema::table('work_orders', function (Blueprint $table) {
            // Payment Tracking Columns
            $table->integer('total_transaksi')->default(0)->after('shipping_cost'); // Total keseluruhan transaksi
            $table->integer('total_paid')->default(0)->after('total_transaksi'); // Total yang sudah dibayar
            $table->integer('sisa_tagihan')->default(0)->after('total_paid'); // Sisa yang belum dibayar
            $table->string('status_pembayaran')->nullable()->after('sisa_tagihan'); // DP/Cicil, Lunas, Belum Bayar
            $table->string('category_spk')->nullable()->after('status_pembayaran'); // Pickup, Delivery, etc
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn([
                'total_transaksi',
                'total_paid',
                'sisa_tagihan',
                'status_pembayaran',
                'category_spk'
            ]);
        });
    }
};
