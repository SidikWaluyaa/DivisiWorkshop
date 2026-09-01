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
        Schema::table('work_order_services', function (Blueprint $table) {
            if (!Schema::hasColumn('work_order_services', 'started_at')) {
                $table->dateTime('started_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('work_order_services', 'completed_at')) {
                $table->dateTime('completed_at')->nullable()->after('started_at');
            }
            if (!Schema::hasColumn('work_order_services', 'actual_duration_minutes')) {
                $table->integer('actual_duration_minutes')->nullable()->after('completed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order_services', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'completed_at', 'actual_duration_minutes']);
        });
    }
};
