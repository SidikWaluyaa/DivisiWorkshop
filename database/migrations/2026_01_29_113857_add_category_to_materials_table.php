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
        Schema::table('materials', function (Blueprint $table) {
            $table->enum('category', ['SHOPPING', 'PRODUCTION'])
                  ->default('PRODUCTION')
                  ->after('type')
                  ->comment('Material category: SHOPPING (no stock check) or PRODUCTION (stock-dependent)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
