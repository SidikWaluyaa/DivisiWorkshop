<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * GAP Resolution PRD-SRS v1.0 (SRS §3.3 Rak Sortir, SRS §5.1 Rak Belanja):
     * Tambah kolom workshop_zone pada storage_racks untuk membedakan
     * rak berdasarkan zona kerja workshop:
     * - sortir_tunggu_belanja: Rak Tunggu Belanja (menunggu bahan baku dari Finlog)
     * - sortir_siap_produksi: Rak Siap Produksi (sudah lengkap, OTW Produksi)
     * - produksi_antrian: Rak Antrean Produksi
     * - post_qc: Rak Post QC / Staging Outbound
     * NULL = rak office/gudang biasa (behaviour lama tidak terganggu)
     */
    public function up(): void
    {
        Schema::table('storage_racks', function (Blueprint $table) {
            $table->string('workshop_zone', 30)->nullable()->after('category')
                  ->comment('Zona workshop: sortir_tunggu_belanja, sortir_siap_produksi, produksi_antrian, post_qc');
            $table->index('workshop_zone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storage_racks', function (Blueprint $table) {
            $table->dropIndex(['workshop_zone']);
            $table->dropColumn('workshop_zone');
        });
    }
};
