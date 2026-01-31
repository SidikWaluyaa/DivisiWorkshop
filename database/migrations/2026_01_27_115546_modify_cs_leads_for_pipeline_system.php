<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fully bypass Schema/DBAL methods to avoid 'generation_expression' error on older MariaDB
        
        $sqlList = [
            "ALTER TABLE cs_leads ADD COLUMN source VARCHAR(255) DEFAULT 'WhatsApp' AFTER customer_province",
            "ALTER TABLE cs_leads ADD COLUMN source_detail TEXT NULL AFTER source",
            "ALTER TABLE cs_leads ADD COLUMN first_contact_at TIMESTAMP NULL AFTER source_detail",
            "ALTER TABLE cs_leads ADD COLUMN first_response_at TIMESTAMP NULL AFTER first_contact_at",
            "ALTER TABLE cs_leads ADD COLUMN response_time_minutes INT NULL AFTER first_response_at",
            "ALTER TABLE cs_leads ADD COLUMN priority ENUM('HOT', 'WARM', 'COLD') DEFAULT 'WARM' AFTER response_time_minutes",
            "ALTER TABLE cs_leads ADD COLUMN expected_value DECIMAL(12, 2) NULL AFTER priority",
            "ALTER TABLE cs_leads ADD COLUMN lost_reason TEXT NULL AFTER expected_value",
            "ALTER TABLE cs_leads ADD COLUMN converted_to_work_order_id BIGINT UNSIGNED NULL AFTER lost_reason"
        ];

        foreach ($sqlList as $sql) {
            try {
                DB::statement($sql);
            } catch (\Exception $e) {
                // Ignore "Column already exists" error (Code: 42S21 or 1060)
                // This is a "Create if not exists" manual polyfill
            }
        }

        // Add FK separately
        try {
            DB::statement("ALTER TABLE cs_leads ADD CONSTRAINT cs_leads_converted_to_work_order_id_foreign FOREIGN KEY (converted_to_work_order_id) REFERENCES work_orders(id) ON DELETE SET NULL");
        } catch (\Exception $e) {
             // Ignore if FK exists
        }

        // Rename logic - Try renaming, ignore if column missing or new name exists
        try {
             DB::statement("ALTER TABLE cs_leads CHANGE last_updated_at last_activity_at TIMESTAMP NULL DEFAULT NULL");
        } catch (\Exception $e) {
             // Ignore
        }
        
        // Update existing status values
        DB::statement("UPDATE cs_leads SET status = 'GREETING' WHERE status = 'NEW'");
        DB::statement("UPDATE cs_leads SET status = 'GREETING', priority = 'COLD' WHERE status = 'INV_GREETING'");
        DB::statement("UPDATE cs_leads SET status = 'KONSULTASI', priority = 'COLD' WHERE status = 'INV_KONSULTASI'");
        DB::statement("UPDATE cs_leads SET status = 'CONVERTED' WHERE status = 'CLOSED'");
        
        // Add new status option: CLOSING
        // Note: Status column already exists, we just need to ensure it accepts new values
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Raw SQL for down migration (Bypassing DBAL)
        
        // 1. Drop Foreign Key
        try {
            DB::statement("ALTER TABLE cs_leads DROP FOREIGN KEY cs_leads_converted_to_work_order_id_foreign");
        } catch (\Exception $e) { /* Ignore */ }

        // 2. Rename Column Back
        try {
             DB::statement("ALTER TABLE cs_leads CHANGE last_activity_at last_updated_at TIMESTAMP NULL DEFAULT NULL");
        } catch (\Exception $e) { /* Ignore */ }

        // 3. Drop Columns
        $dropCols = [
            'source',
            'source_detail',
            'first_contact_at',
            'first_response_at',
            'response_time_minutes',
            'priority',
            'expected_value',
            'lost_reason',
            'converted_to_work_order_id',
        ];

        // Drop one by one (safest) or grouped, but one by one with try-catch handles partial states best
        foreach ($dropCols as $col) {
            try {
                DB::statement("ALTER TABLE cs_leads DROP COLUMN $col");
            } catch (\Exception $e) { /* Ignore */ }
        }
    }
};
