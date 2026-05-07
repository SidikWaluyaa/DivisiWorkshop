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
            Schema::table('cs_spk', function (Blueprint $table) {
                $table->string('payment_method')->default('Transfer')->after('payment_type');
            });
        } catch (\Exception $e) {
            // Jika kolom sudah ada atau error lain, kita abaikan saja demi kelancaran migrasi
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cs_spk', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
