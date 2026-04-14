<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cs_quotation_items', function (Blueprint $table) {
            $table->json('requested_materials')->nullable()->after('services');
        });

        Schema::table('cs_spk_items', function (Blueprint $table) {
            $table->json('requested_materials')->nullable()->after('services');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cs_quotation_items', function (Blueprint $table) {
            $table->dropColumn('requested_materials');
        });

        Schema::table('cs_spk_items', function (Blueprint $table) {
            $table->dropColumn('requested_materials');
        });
    }

};
