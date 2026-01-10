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
            $table->timestamp('prep_washing_started_at')->nullable()->after('prep_washing_by');
            $table->timestamp('prep_sol_started_at')->nullable()->after('prep_sol_by');
            $table->timestamp('prep_upper_started_at')->nullable()->after('prep_upper_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['prep_washing_started_at', 'prep_sol_started_at', 'prep_upper_started_at']);
        });
    }
};
