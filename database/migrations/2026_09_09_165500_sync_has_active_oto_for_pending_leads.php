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
        // 1. Set has_active_oto = 1 for all work orders with ongoing OTO execution (ACCEPTED or IN_PROGRESS)
        DB::statement("
            UPDATE work_orders 
            SET has_active_oto = 1 
            WHERE id IN (
                SELECT DISTINCT work_order_id 
                FROM otos 
                WHERE status IN ('ACCEPTED', 'IN_PROGRESS') 
                AND deleted_at IS NULL
            )
        ");

        // 2. Reset has_active_oto = 0 for work orders that do not have an active ACCEPTED or IN_PROGRESS OTO
        DB::statement("
            UPDATE work_orders 
            SET has_active_oto = 0 
            WHERE id NOT IN (
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
