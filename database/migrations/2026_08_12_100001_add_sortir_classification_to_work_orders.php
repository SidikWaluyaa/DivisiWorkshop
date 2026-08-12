<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * GAP Resolution PRD-SRS v1.0 (FR-3.1):
     * Tambah field klasifikasi Sortir pada SPK.
     * - perlu_bongkar: Apakah sepatu perlu dibongkar komponen (sol/upper terpisah).
     * - perlu_belanja: Apakah sepatu membutuhkan bahan baku dari eksternal (Finlog).
     * Keduanya diisi manual oleh Staff/Admin Sortir saat tahap klasifikasi.
     */
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->boolean('perlu_bongkar')->default(false)->after('is_revising')
                  ->comment('Klasifikasi Sortir: perlu bongkar komponen sepatu');
            $table->boolean('perlu_belanja')->default(false)->after('perlu_bongkar')
                  ->comment('Klasifikasi Sortir: perlu belanja bahan baku via Finlog');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['perlu_bongkar', 'perlu_belanja']);
        });
    }
};
