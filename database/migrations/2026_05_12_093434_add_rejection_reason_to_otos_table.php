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
            $table->string('rejection_reason')->nullable()->after('status');
            $table->text('rejection_notes')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('otos', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'rejection_notes']);
        });
    }
};
