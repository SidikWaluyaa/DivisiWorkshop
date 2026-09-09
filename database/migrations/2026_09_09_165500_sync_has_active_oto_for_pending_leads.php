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
        // Reset has_active_oto = false for work orders that do not have an active ACCEPTED or IN_PROGRESS OTO
        DB::statement("
            UPDATE work_orders 
            SET has_active_oto = 0 
            WHERE has_active_oto = 1 
            AND id NOT IN (
                SELECT DISTINCT work_order_id 
                FROM otos 
                WHERE status IN ('ACCEPTED', 'IN_PROGRESS') 
                AND deleted_at IS NULL
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
