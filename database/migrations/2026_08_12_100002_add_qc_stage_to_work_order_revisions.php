<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * GAP Resolution PRD-SRS v1.0 (FR-6.1, FR-7.1):
     * Tambah kolom qc_stage pada work_order_revisions untuk membedakan
     * revisi yang berasal dari QC Produksi vs QC Akhir.
     * Batas rework maksimal 2x dihitung per stage (bukan total).
     */
    public function up(): void
    {
        Schema::table('work_order_revisions', function (Blueprint $table) {
            $table->string('qc_stage', 20)->nullable()->after('origin_status')
                  ->comment('Tahap QC asal revisi: PRODUKSI atau AKHIR');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order_revisions', function (Blueprint $table) {
            $table->dropColumn('qc_stage');
        });
    }
};
