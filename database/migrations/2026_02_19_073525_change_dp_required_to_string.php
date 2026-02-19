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
            $table->string('dp_required')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otos', function (Blueprint $table) {
            $table->decimal('dp_required', 10, 2)->change();
        });
    }
};
