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
        Schema::table('cs_spk', function (Blueprint $table) {
            $table->integer('total_items')->default(1)->after('spk_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cs_spk', function (Blueprint $table) {
            $table->dropColumn('total_items');
        });
    }
};
