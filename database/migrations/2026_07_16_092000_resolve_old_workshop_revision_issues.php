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
        DB::table('cx_issues')
            ->where('status', 'OPEN')
            ->where(function ($query) {
                $query->where('category', 'like', 'Revisi %')
                      ->orWhere('source', 'like', 'WORKSHOP_%');
            })
            ->update([
                'status' => 'RESOLVED',
                'resolution_notes' => 'Auto-resolved: Pembersihan antrean revisi internal workshop lama.',
                'resolved_at' => now(),
                'resolved_by' => 1, // Default to first user/admin
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for data cleanup
    }
};
