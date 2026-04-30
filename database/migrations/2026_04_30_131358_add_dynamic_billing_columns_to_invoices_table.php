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
        Schema::table('invoices', function (Blueprint $table) {
            // Standard columns (Update manually via Model to support older DB versions)
            $table->decimal('total_dp_with_code', 15, 2)->default(0)->after('dp_unique_code');
            $table->decimal('total_pelunasan_with_code', 15, 2)->default(0)->after('final_unique_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['total_dp_with_code', 'total_pelunasan_with_code']);
        });
    }
};
