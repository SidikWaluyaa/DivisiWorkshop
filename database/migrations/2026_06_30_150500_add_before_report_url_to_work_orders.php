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
        // 1. Add before_report_url column to work_orders only if it does not exist
        if (!Schema::hasColumn('work_orders', 'before_report_url')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->string('before_report_url')->nullable()->after('finish_report_url');
            });
        }

        // 2. Pre-populate before_report_url for all existing work orders
        $baseUrl = config('app.url') ?? 'http://sistemworkshop.test';

        DB::table('work_orders')->orderBy('id')->chunk(100, function ($orders) use ($baseUrl) {
            foreach ($orders as $order) {
                $token = $order->invoice_token;
                
                // If invoice_token is empty, generate a new random 32 character token
                if (empty($token)) {
                    $token = Str::random(32);
                    DB::table('work_orders')
                        ->where('id', $order->id)
                        ->update(['invoice_token' => $token]);
                }

                $spkSlug = Str::slug($order->spk_number);
                $beforeUrl = $baseUrl . "/laporan-before/" . $spkSlug . "/" . $token;

                DB::table('work_orders')
                    ->where('id', $order->id)
                    ->update(['before_report_url' => $beforeUrl]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('work_orders', 'before_report_url')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->dropColumn('before_report_url');
            });
        }
    }
};
