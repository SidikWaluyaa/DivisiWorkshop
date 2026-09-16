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
        // 1. Auto-complete any active OTOs for work orders that are already physically taken by customer
        DB::statement("
            UPDATE otos
            JOIN work_orders ON otos.work_order_id = work_orders.id
            SET otos.status = 'COMPLETED',
                otos.completed_at = COALESCE(work_orders.taken_date, NOW())
            WHERE work_orders.taken_date IS NOT NULL
              AND otos.status IN ('ACCEPTED', 'IN_PROGRESS')
        ");

        // 2. Clear has_active_oto flag for work orders that are already physically taken by customer
        DB::statement("
            UPDATE work_orders
            SET has_active_oto = 0,
                current_location = 'Sudah Diambil Pelanggan'
            WHERE taken_date IS NOT NULL
              AND has_active_oto = 1
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op for data healing
    }
};
