<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('invoice_token', 64)->nullable()->unique()->after('status_pembayaran');
            $table->text('invoice_awal')->nullable()->after('invoice_token');
            $table->text('invoice_akhir')->nullable()->after('invoice_awal');
        });

        // Backfill existing orders
        $baseUrl = config('app.url', 'http://localhost');
        $orders = \App\Models\WorkOrder::all();
        foreach ($orders as $order) {
            // 1. Generate Invoice Token if missing
            if (!$order->invoice_token) {
                $order->invoice_token = Str::random(32);
            }

            // 2. Recalculate all Finance fields (persists status_pembayaran, sisa_tagihan, total_transaksi)
            // This fixes the NULL issue the user mentioned.
            $order->recalculateTotalPrice(true);

            // 3. Set the URLs
            $order->invoice_awal = $baseUrl . "/api/invoice_share.php?type=awal&token=" . $order->invoice_token;
            if ($order->status_pembayaran === 'L') {
                $order->invoice_akhir = $baseUrl . "/api/invoice_share.php?type=akhir&token=" . $order->invoice_token;
            }
            
            $order->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['invoice_token', 'invoice_awal', 'invoice_akhir']);
        });
    }
};
