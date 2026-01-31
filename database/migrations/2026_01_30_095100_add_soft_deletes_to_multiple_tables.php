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
        $tables = [
            'cs_leads',
            'cs_quotations',
            'cs_spk',
            'otos',
            'material_requests',
            'purchases',
            'materials',
            'customers'
        ];

        foreach ($tables as $table) {
            try {
                DB::statement("ALTER TABLE $table ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
            } catch (\Exception $e) {
                // Ignore "Column already exists" (Code 1060/42S21)
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'cs_leads',
            'cs_quotations',
            'cs_spk',
            'otos',
            'material_requests',
            'purchases',
            'materials',
            'customers'
        ];

        foreach ($tables as $table) {
            try {
                DB::statement("ALTER TABLE $table DROP COLUMN deleted_at");
            } catch (\Exception $e) {
                // Ignore "Column doesn't exist"
            }
        }
    }
};
