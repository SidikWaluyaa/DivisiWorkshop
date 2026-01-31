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
            $table->string('shoe_brand')->nullable();
            $table->string('shoe_type')->nullable();
            $table->string('shoe_color')->nullable();
            $table->string('shoe_size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cs_quotations', function (Blueprint $table) {
            $table->dropColumn(['shoe_brand', 'shoe_type', 'shoe_color', 'shoe_size']);
        });
    }
};
