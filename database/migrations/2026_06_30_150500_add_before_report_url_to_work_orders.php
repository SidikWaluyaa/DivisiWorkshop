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
        // 1. Add before_report_url column using raw SQL to bypass MySQL/MariaDB version compatibility issues
        try {
            DB::statement("ALTER TABLE `work_orders` ADD `before_report_url` VARCHAR(191) NULL AFTER `finish_report_url`");
        } catch (\Exception $e) {
            // Column already exists, ignore error
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
        try {
            DB::statement("ALTER TABLE `work_orders` DROP COLUMN `before_report_url`");
        } catch (\Exception $e) {
            // Ignore if column doesn't exist
        }
    }
};
