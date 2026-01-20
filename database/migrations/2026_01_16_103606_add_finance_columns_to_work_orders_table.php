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
            // Finance & SPK Data
            $table->string('transaction_type')->nullable()->after('priority'); // Reguler/Express
            $table->string('source_jasa')->nullable()->after('transaction_type'); // Sumber Jasa
            
            // Costs
            $table->integer('total_service_price')->default(0)->after('status');
            $table->integer('cost_oto')->default(0)->after('total_service_price'); // Biaya OTO
            $table->integer('cost_add_service')->default(0)->after('cost_oto'); // Tambah Jasa
            $table->integer('shipping_cost')->default(0)->after('cost_add_service'); // Ongkir
            
            // Statuses
            $table->string('payment_status_detail')->nullable()->after('shipping_cost'); // DP, Lunas Awal (LA), Belum Bayar (BB), Garansi, Cancel, Donasi
            $table->string('final_status')->nullable()->after('payment_status_detail'); // TL, TN, L, Cancel, Garansi, LA, Lelang
            
            // Timestamps & PIC
            $table->timestamp('finance_entry_at')->nullable();
            $table->timestamp('finance_exit_at')->nullable();
            $table->unsignedBigInteger('pic_finance_id')->nullable();
            $table->foreign('pic_finance_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            //
        });
    }
};
