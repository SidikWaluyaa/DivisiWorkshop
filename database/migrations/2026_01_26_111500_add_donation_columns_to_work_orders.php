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
            $table->integer('reminder_count')->default(0)->after('status');
            $table->timestamp('last_reminder_at')->nullable()->after('reminder_count');
            $table->timestamp('donated_at')->nullable()->after('last_reminder_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['reminder_count', 'last_reminder_at', 'donated_at']);
        });
    }
};
