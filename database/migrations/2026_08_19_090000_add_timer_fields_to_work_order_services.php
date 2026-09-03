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
            $table->dateTime('started_at')->nullable()->after('status');
            $table->dateTime('completed_at')->nullable()->after('started_at');
            $table->integer('actual_duration_minutes')->nullable()->after('completed_at');
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
