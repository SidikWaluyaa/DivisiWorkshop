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

        // 2. Ensure invoice_token is populated for any rare cases where it is empty (only queries empty ones, highly optimized)
        DB::table('work_orders')
            ->whereNull('invoice_token')
            ->orWhere('invoice_token', '')
            ->orderBy('id')
            ->chunk(100, function ($orders) {
                foreach ($orders as $order) {
                    DB::table('work_orders')
                        ->where('id', $order->id)
                        ->update(['invoice_token' => \Illuminate\Support\Str::random(32)]);
                }
            });

        // 3. Fast bulk update before_report_url using a single optimized MySQL query (Instant execution)
        $baseUrl = rtrim(config('app.url') ?? 'http://sistemworkshop.test', '/');

        DB::statement("
            UPDATE `work_orders` 
            SET `before_report_url` = CONCAT(
                ?, 
                '/laporan-before/', 
                LOWER(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(spk_number), ' ', '-'), '/', '-'), '\\\\', '-'), '--', '-')), 
                '/', 
                `invoice_token`
            )
            WHERE `before_report_url` IS NULL OR `before_report_url` = ''
        ", [$baseUrl]);
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
