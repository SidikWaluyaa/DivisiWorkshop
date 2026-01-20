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
            $table->boolean('has_active_oto')->default(false)->after('status');
            $table->decimal('oto_addition_amount', 10, 2)->default(0)->after('has_active_oto');
            $table->decimal('oto_discount_amount', 10, 2)->default(0)->after('oto_addition_amount');
            $table->integer('oto_priority_boost')->default(0)->after('oto_discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn([
                'has_active_oto',
                'oto_addition_amount',
                'oto_discount_amount',
                'oto_priority_boost'
            ]);
        });
    }
};
