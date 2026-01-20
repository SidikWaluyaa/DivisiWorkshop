<?php

/**
 * Migration Category: Work Orders Extension
 * Purpose: Add reception fields for warehouse workflow
 * Module: Reception
 * Dependencies: 2024_01_01_000005_create_work_orders_table.php
 * Date: 2026-01-16
 */

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
            $table->longText('accessories_data')->nullable()->after('status');
            $table->boolean('reception_qc_passed')->default(true)->after('accessories_data');
            $table->text('reception_rejection_reason')->nullable()->after('reception_qc_passed');
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['accessories_data', 'reception_qc_passed', 'reception_rejection_reason']);
        });
    }
};
