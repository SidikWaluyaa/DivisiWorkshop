<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * GAP Resolution PRD-SRS v1.0 (FR-4.1, FR-4.2):
     * 1. Tambah kolom finlog_request_id untuk tracking ID pengajuan di sistem Finlog eksternal.
     * 2. Tambah status RECEIVED pada enum status material_requests.
     *    RECEIVED = bahan baku sudah diterima dari supplier (trigger dari Finlog Webhook).
     */
    public function up(): void
    {
        Schema::table('material_requests', function (Blueprint $table) {
            $table->string('finlog_request_id')->nullable()->after('oto_id')
                  ->comment('ID pengajuan belanja di sistem Finlog eksternal');
            $table->index('finlog_request_id');
        });

        // Alter ENUM to add RECEIVED status
        // MySQL doesn't support adding values to ENUM natively with Blueprint,
        // so we use raw DB statement
        DB::statement("ALTER TABLE material_requests MODIFY COLUMN status ENUM('PENDING','APPROVED','REJECTED','PURCHASED','RECEIVED','CANCELLED') DEFAULT 'PENDING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert ENUM (remove RECEIVED) — first update any RECEIVED rows to PURCHASED
        DB::statement("UPDATE material_requests SET status = 'PURCHASED' WHERE status = 'RECEIVED'");
        DB::statement("ALTER TABLE material_requests MODIFY COLUMN status ENUM('PENDING','APPROVED','REJECTED','PURCHASED','CANCELLED') DEFAULT 'PENDING'");

        Schema::table('material_requests', function (Blueprint $table) {
            $table->dropIndex(['finlog_request_id']);
            $table->dropColumn('finlog_request_id');
        });
    }
};
