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
            $table->dropColumn(['pic_name', 'pic_phone']);
            $table->foreignId('pic_user_id')->nullable()->after('status')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['pic_user_id']);
            $table->dropColumn('pic_user_id');
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
        });
    }
};
