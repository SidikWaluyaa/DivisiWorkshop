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
        // 1. Sync type column from order_payments to invoice_payments where type is currently NULL
        DB::statement("
            UPDATE invoice_payments ip
            INNER JOIN order_payments op ON ip.invoice_id = op.invoice_id 
                AND ABS(ip.amount - op.amount_total) < 0.01
            SET ip.type = UPPER(op.type)
            WHERE ip.type IS NULL AND op.type IS NOT NULL
        ");

        // 2. Set any remaining NULL types to 'BEFORE' (most common default for CS payments)
        DB::statement("
            UPDATE invoice_payments 
            SET type = 'BEFORE' 
            WHERE type IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed for rollback as this is a one-way historical data sync
    }
};
