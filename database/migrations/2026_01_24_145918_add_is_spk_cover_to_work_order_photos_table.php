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
            $table->boolean('is_spk_cover')->default(false)->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order_photos', function (Blueprint $table) {
            $table->dropColumn('is_spk_cover');
        });
    }
};
