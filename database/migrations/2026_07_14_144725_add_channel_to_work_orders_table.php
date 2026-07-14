<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add channel column
        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('channel')->nullable()->after('customer_email');
        });

        // 2. Backfill channel value from cs_leads to work_orders for orders that have CS leads
        DB::table('work_orders')
            ->join('cs_leads', 'work_orders.id', '=', 'cs_leads.converted_to_work_order_id')
            ->update(['work_orders.channel' => DB::raw('cs_leads.channel')]);

        // 3. Set remaining NULL channels (Walk-in/Direct orders) to 'OFFLINE'
        DB::table('work_orders')
            ->whereNull('channel')
            ->update(['channel' => 'OFFLINE']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('channel');
        });
    }
};
