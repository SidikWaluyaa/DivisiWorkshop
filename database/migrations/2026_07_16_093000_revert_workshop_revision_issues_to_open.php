<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kembalikan semua data yang terlanjur di-resolve oleh migrasi pembersihan sebelumnya di server live
        DB::table('cx_issues')
            ->where('resolution_notes', 'Auto-resolved: Pembersihan antrean revisi internal workshop lama.')
            ->update([
                'status' => 'OPEN',
                'resolution_notes' => null,
                'resolved_at' => null,
                'resolved_by' => null
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
