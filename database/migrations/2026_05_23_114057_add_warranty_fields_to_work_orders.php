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
            // Garansi yang diberikan saat pickup (null = tidak ada garansi)
            $table->unsignedTinyInteger('warranty_duration_months')->nullable()->after('taken_date');
            // Auto-hitung: taken_date + warranty_duration_months
            $table->dateTime('warranty_expires_at')->nullable()->after('warranty_duration_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['warranty_duration_months', 'warranty_expires_at']);
        });
    }
};
