<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * GAP Resolution PRD-SRS v1.0 (SRS §3.5 Handover, FR-8.1):
     * Tabel surat_jalan untuk dokumen serah-terima fisik sepatu antar titik kerja.
     * 3 Titik serah-terima:
     *   1. Sortir  ➡️  Produksi  (sortir_to_produksi)
     *   2. Produksi ➡️  Post QC   (produksi_to_post_qc)
     *   3. Post QC  ➡️  Office    (post_qc_to_office)
     *
     * Setiap surat jalan berisi referensi ke SPK,
     * pengirim & penerima (user_id), serta timestamp serah-terima.
     */
    public function up(): void
    {
        Schema::create('surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique()
                  ->comment('Nomor surat jalan: SJ-YYYYMMDD-0001');
            $table->string('jenis_serah_terima', 30)
                  ->comment('sortir_to_produksi, produksi_to_post_qc, post_qc_to_office');

            // Pengirim
            $table->foreignId('pengirim_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('dikirim_at')->nullable()
                  ->comment('Waktu barang diserahkan oleh pengirim');

            // Penerima
            $table->foreignId('penerima_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diterima_at')->nullable()
                  ->comment('Waktu barang diterima oleh penerima');

            // Status dokumen
            $table->string('status', 20)->default('DRAFT')
                  ->comment('DRAFT, DIKIRIM, DITERIMA, BATAL');

            $table->text('catatan')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('jenis_serah_terima');
            $table->index('status');
        });

        // Pivot: surat_jalan_items — SPK yang masuk dalam surat jalan
        Schema::create('surat_jalan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_jalan_id')->constrained('surat_jalan')->cascadeOnDelete();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->text('kondisi_serah_terima')->nullable()
                  ->comment('Catatan kondisi sepatu saat serah-terima');
            $table->timestamps();

            $table->unique(['surat_jalan_id', 'work_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_items');
        Schema::dropIfExists('surat_jalan');
    }
};
