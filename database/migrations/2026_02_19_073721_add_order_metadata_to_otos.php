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
        Schema::table('otos', function (Blueprint $table) {
            $table->string('spk_number')->nullable()->after('work_order_id');
            $table->string('customer_name')->nullable()->after('spk_number');
            $table->string('customer_phone')->nullable()->after('customer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otos', function (Blueprint $table) {
            $table->dropColumn(['spk_number', 'customer_name', 'customer_phone']);
        });
    }
};
