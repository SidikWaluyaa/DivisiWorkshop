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
        Schema::table('cs_quotations', function (Blueprint $table) {
            $table->longText('requested_materials')->after('notes')->nullable();
        });

        Schema::table('cs_spk', function (Blueprint $table) {
            $table->longText('requested_materials')->after('special_instructions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cs_quotations', function (Blueprint $table) {
            $table->dropColumn('requested_materials');
        });

        Schema::table('cs_spk', function (Blueprint $table) {
            $table->dropColumn('requested_materials');
        });
    }
};
