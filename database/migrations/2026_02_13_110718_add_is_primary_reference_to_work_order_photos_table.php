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
        Schema::table('work_order_photos', function (Blueprint $table) {
            $table->boolean('is_primary_reference')->default(false)->after('is_spk_cover');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order_photos', function (Blueprint $table) {
            $table->dropColumn('is_primary_reference');
        });
    }
};
