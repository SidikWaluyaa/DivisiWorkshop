<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Restores OTOs that were mistakenly marked as COMPLETED by the previous migration
     * for shoes that HAVE NOT been taken by customers (taken_date IS NULL).
     */
    public function up(): void
    {
        // 1. Revert OTO status back to IN_PROGRESS or ACCEPTED for untaken shoes
        DB::statement("
            UPDATE otos
            JOIN work_orders ON otos.work_order_id = work_orders.id
            SET otos.status = IF(otos.started_at IS NOT NULL, 'IN_PROGRESS', 'ACCEPTED'),
                otos.completed_at = NULL
            WHERE work_orders.taken_date IS NULL
              AND otos.status = 'COMPLETED'
              AND otos.oto_completed_by IS NULL
        ");

        // 2. Restore has_active_oto flag on work_orders that still have active OTO
        DB::statement("
            UPDATE work_orders
            JOIN otos ON work_orders.id = otos.work_order_id
            SET work_orders.has_active_oto = 1,
                work_orders.current_location = 'Stasiun OTO (Workshop)'
            WHERE work_orders.taken_date IS NULL
              AND otos.status IN ('ACCEPTED', 'IN_PROGRESS')
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
