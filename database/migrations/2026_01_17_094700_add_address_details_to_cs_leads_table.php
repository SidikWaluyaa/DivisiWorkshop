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
        try {
            Schema::table('cs_leads', function (Blueprint $table) {
                $table->string('customer_city')->nullable()->after('customer_address');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Ignore duplicate column error (1060)
            if ($e->errorInfo[1] != 1060) {
                throw $e;
            }
        }

        try {
            Schema::table('cs_leads', function (Blueprint $table) {
                $table->string('customer_province')->nullable()->after('customer_city');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Ignore duplicate column error (1060)
            if ($e->errorInfo[1] != 1060) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cs_leads', function (Blueprint $table) {
            $table->dropColumn(['customer_city', 'customer_province']);
        });
    }
};
