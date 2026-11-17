<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->timestamp('entry_date')->nullable()->default(null)->change();
        });

        // Clear entry_date on SPK_PENDING orders that have not been physically received yet
        DB::table('work_orders')
            ->where('status', 'SPK_PENDING')
            ->whereNotNull('entry_date')
            ->update(['entry_date' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->timestamp('entry_date')->useCurrent()->change();
        });
    }
};
