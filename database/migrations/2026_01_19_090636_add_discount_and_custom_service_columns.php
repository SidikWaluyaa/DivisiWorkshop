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
            $table->decimal('discount', 15, 2)->default(0)->after('status');
        });

        Schema::table('work_order_services', function (Blueprint $table) {
            $table->string('custom_name')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('discount');
        });

        Schema::table('work_order_services', function (Blueprint $table) {
            $table->dropColumn('custom_name');
        });
    }
};
