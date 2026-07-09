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
        Schema::table('services', function (Blueprint $table) {
            $table->string('allow_fast_track', 10)->default('no')->after('price');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('fast_track_status', 10)->default('no')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('allow_fast_track');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('fast_track_status');
        });
    }
};
