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
        Schema::table('cx_issues', function (Blueprint $table) {
            $table->text('kendala_1')->nullable()->after('opsi_solusi');
            $table->text('kendala_2')->nullable()->after('kendala_1');
            $table->text('opsi_solusi_1')->nullable()->after('kendala_2');
            $table->text('opsi_solusi_2')->nullable()->after('opsi_solusi_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cx_issues', function (Blueprint $table) {
            $table->dropColumn(['kendala_1', 'kendala_2', 'opsi_solusi_1', 'opsi_solusi_2']);
        });
    }
};
