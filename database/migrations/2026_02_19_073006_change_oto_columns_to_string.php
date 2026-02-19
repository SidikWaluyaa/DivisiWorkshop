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
        Schema::table('otos', function (Blueprint $table) {
            $table->string('total_normal_price')->change();
            $table->string('total_oto_price')->change();
            $table->string('total_discount')->change();
            $table->text('proposed_services')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otos', function (Blueprint $table) {
            $table->decimal('total_normal_price', 10, 2)->change();
            $table->decimal('total_oto_price', 10, 2)->change();
            $table->decimal('total_discount', 10, 2)->change();
            $table->longText('proposed_services')->change();
        });
    }
};
