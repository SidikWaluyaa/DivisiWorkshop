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
        Schema::table('services', function (Blueprint $table) {
            $table->integer('hk_days')->nullable()->after('duration_minutes');
        });

        Schema::table('cs_quotation_items', function (Blueprint $table) {
            $table->integer('hk_days')->default(0)->after('item_total_price');
            $table->boolean('is_warranty')->default(false)->after('hk_days');
        });

        Schema::table('cs_spk_items', function (Blueprint $table) {
            $table->integer('hk_days')->default(0)->after('item_total_price');
            $table->boolean('is_warranty')->default(false)->after('hk_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('hk_days');
        });

        Schema::table('cs_quotation_items', function (Blueprint $table) {
            $table->dropColumn(['hk_days', 'is_warranty']);
        });

        Schema::table('cs_spk_items', function (Blueprint $table) {
            $table->dropColumn(['hk_days', 'is_warranty']);
        });
    }
};
