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
            $table->string('category')->nullable()->after('shoe_brand');
            $table->string('shoe_size')->nullable()->after('shoe_brand');
            $table->string('priority')->default('Reguler')->after('shoe_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cs_spk', function (Blueprint $table) {
            $table->dropColumn(['category', 'shoe_size', 'priority']);
        });
    }
};
