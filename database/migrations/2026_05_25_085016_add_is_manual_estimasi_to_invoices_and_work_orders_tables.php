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
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('is_manual_estimasi')->default(false)->after('estimasi_selesai');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->boolean('is_manual_estimasi')->default(false)->after('estimation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('is_manual_estimasi');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('is_manual_estimasi');
        });
    }
};
