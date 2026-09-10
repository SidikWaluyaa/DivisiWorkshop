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
        // 1. Move any SPKs with active/in-progress OTO from PRODUCTION to SELESAI status
        // so they are exclusively handled in Stasiun OTO (/oto) and not duplicated in Produksi
        DB::statement("
            UPDATE work_orders 
            SET status = 'SELESAI', 
                current_location = 'Stasiun OTO (Workshop)',
                has_active_oto = 1
            WHERE id IN (
                SELECT DISTINCT work_order_id 
                FROM otos 
                WHERE status IN ('ACCEPTED', 'IN_PROGRESS') 
                AND deleted_at IS NULL
            )
            AND status = 'PRODUCTION'
        ");

        // 2. Normalize legacy 'OTO' priority label to 'Prioritas'
        DB::statement("
            UPDATE work_orders 
            SET priority = 'Prioritas' 
            WHERE priority = 'OTO'
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
