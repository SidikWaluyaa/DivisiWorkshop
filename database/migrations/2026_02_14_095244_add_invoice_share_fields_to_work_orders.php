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
        $orders = DB::table('work_orders')->get();
        foreach ($orders as $order) {
            $token = Str::random(32);
            $awalUrl = $baseUrl . "/api/invoice_share.php?type=awal&token=" . $token;
            $akhirUrl = ($order->status_pembayaran === 'L') 
                ? $baseUrl . "/api/invoice_share.php?type=akhir&token=" . $token 
                : null;

            DB::table('work_orders')->where('id', $order->id)->update([
                'invoice_token' => $token,
                'invoice_awal' => $awalUrl,
                'invoice_akhir' => $akhirUrl
            ]);
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
