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
        Schema::table('cx_issues', function (Blueprint $table) {
            $table->text('rec_service_1')->nullable()->after('recommended_services');
            $table->text('rec_service_2')->nullable()->after('rec_service_1');
            $table->text('sug_service_1')->nullable()->after('rec_service_2');
            $table->text('sug_service_2')->nullable()->after('sug_service_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cx_issues', function (Blueprint $table) {
            $table->dropColumn(['rec_service_1', 'rec_service_2', 'sug_service_1', 'sug_service_2']);
        });
    }
};
