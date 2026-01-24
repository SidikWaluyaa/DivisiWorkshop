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
        Schema::table('storage_racks', function (Blueprint $table) {
            $table->enum('category', ['shoes', 'accessories'])->default('shoes')->after('rack_code');
        });

        Schema::table('storage_assignments', function (Blueprint $table) {
            $table->enum('item_type', ['shoes', 'accessories'])->default('shoes')->after('rack_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storage_assignments', function (Blueprint $table) {
            $table->dropColumn('item_type');
        });

        Schema::table('storage_racks', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
