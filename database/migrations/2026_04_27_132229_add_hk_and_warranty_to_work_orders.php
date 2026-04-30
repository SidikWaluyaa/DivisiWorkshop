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
        Schema::table('work_orders', function (Blueprint $blueprint) {
            $blueprint->integer('hk_days')->nullable()->after('notes');
            $blueprint->boolean('is_warranty')->default(false)->after('hk_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['hk_days', 'is_warranty']);
        });
    }
};
