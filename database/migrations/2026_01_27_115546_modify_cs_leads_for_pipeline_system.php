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
        // Use Raw SQL to bypass Doctrine DBAL 'generation_expression' error on older MariaDB versions
        $columns = Schema::getColumnListing('cs_leads');
        
        if (!in_array('source', $columns)) {
            DB::statement("ALTER TABLE cs_leads ADD COLUMN source VARCHAR(255) DEFAULT 'WhatsApp' AFTER customer_province");
        }
        if (!in_array('source_detail', $columns)) {
            DB::statement("ALTER TABLE cs_leads ADD COLUMN source_detail TEXT NULL AFTER source");
        }
        if (!in_array('first_contact_at', $columns)) {
            DB::statement("ALTER TABLE cs_leads ADD COLUMN first_contact_at TIMESTAMP NULL AFTER source_detail");
        }
        if (!in_array('first_response_at', $columns)) {
            DB::statement("ALTER TABLE cs_leads ADD COLUMN first_response_at TIMESTAMP NULL AFTER first_contact_at");
        }
        if (!in_array('response_time_minutes', $columns)) {
            DB::statement("ALTER TABLE cs_leads ADD COLUMN response_time_minutes INT NULL AFTER first_response_at");
        }
        if (!in_array('priority', $columns)) {
            DB::statement("ALTER TABLE cs_leads ADD COLUMN priority ENUM('HOT', 'WARM', 'COLD') DEFAULT 'WARM' AFTER response_time_minutes");
        }
        if (!in_array('expected_value', $columns)) {
            DB::statement("ALTER TABLE cs_leads ADD COLUMN expected_value DECIMAL(12, 2) NULL AFTER priority");
        }
        if (!in_array('lost_reason', $columns)) {
            DB::statement("ALTER TABLE cs_leads ADD COLUMN lost_reason TEXT NULL AFTER expected_value");
        }
        if (!in_array('converted_to_work_order_id', $columns)) {
            // Note: Adding constraint separately or ignoring for now to be safe, but best to add.
            DB::statement("ALTER TABLE cs_leads ADD COLUMN converted_to_work_order_id BIGINT UNSIGNED NULL AFTER lost_reason");
            
            // Try adding FK safely, suppress if fails or handle separately
            try {
                Schema::table('cs_leads', function (Blueprint $table) {
                     $table->foreign('converted_to_work_order_id')->references('id')->on('work_orders')->nullOnDelete();
                });
            } catch (\Exception $e) {
                // Ignore FK error if specific driver issue, but column exists
            }
        }

        // Rename logic using Raw SQL
        if (in_array('last_updated_at', $columns) && !in_array('last_activity_at', $columns)) {
             DB::statement("ALTER TABLE cs_leads CHANGE last_updated_at last_activity_at TIMESTAMP NULL DEFAULT NULL");
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
        // Raw SQL for down migration
        
        // 1. Drop Foreign Key (Try/Catch for safety)
        try {
            DB::statement("ALTER TABLE cs_leads DROP FOREIGN KEY cs_leads_converted_to_work_order_id_foreign");
        } catch (\Exception $e) {
             // Ignore if not found
        }

        // 2. Rename Column Back
        $columns = Schema::getColumnListing('cs_leads');
        if (in_array('last_activity_at', $columns)) {
             DB::statement("ALTER TABLE cs_leads CHANGE last_activity_at last_updated_at TIMESTAMP NULL DEFAULT NULL");
        }

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

        // Only drop if they exist
        $existing = array_intersect($dropCols, $columns);
        if (!empty($existing)) {
             $colString = implode(', DROP COLUMN ', $existing);
             DB::statement("ALTER TABLE cs_leads DROP COLUMN $colString");
        }
    }
};
