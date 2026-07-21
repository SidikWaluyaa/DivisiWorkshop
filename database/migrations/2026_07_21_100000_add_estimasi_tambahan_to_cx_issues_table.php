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
            if (!Schema::hasColumn('cx_issues', 'estimasi_tambahan')) {
                $table->string('estimasi_tambahan', 50)->nullable()->after('category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cx_issues', function (Blueprint $table) {
            if (Schema::hasColumn('cx_issues', 'estimasi_tambahan')) {
                $table->dropColumn('estimasi_tambahan');
            }
        });
    }
};
