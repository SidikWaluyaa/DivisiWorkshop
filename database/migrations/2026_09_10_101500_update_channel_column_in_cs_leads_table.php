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
        Schema::table('cs_leads', function (Blueprint $table) {
            $table->string('channel', 50)->default('ONLINE')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cs_leads', function (Blueprint $table) {
            $table->enum('channel', ['ONLINE', 'OFFLINE'])->default('ONLINE')->change();
        });
    }
};
