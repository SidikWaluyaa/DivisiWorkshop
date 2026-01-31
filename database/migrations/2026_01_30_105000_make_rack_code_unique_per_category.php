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
        // 1. Drop Foreign Key on storage_assignments referencing this rack_code
        // We use try-catch because the FK might have different names or already be dropped in partial runs
        try {
            DB::statement("ALTER TABLE storage_assignments DROP FOREIGN KEY storage_assignments_rack_code_foreign");
        } catch (\Exception $e) {
            // Fallback for constraint name if custom
            try {
                DB::statement("ALTER TABLE storage_assignments DROP FOREIGN KEY storage_assignments_rack_code_foreign_ind");
            } catch (\Exception $x) { /* Ignore if not found */ }
        }

        // 2. Drop the existing unique index on rack_code
        try {
            DB::statement("ALTER TABLE storage_racks DROP INDEX storage_racks_rack_code_unique");
        } catch (\Exception $e) { /* Ignore */ }
        try {
             // Standard laravel index name fallback
            DB::statement("ALTER TABLE storage_racks DROP INDEX rack_code");
        } catch (\Exception $e) { /* Ignore */ }

        // 3. Add a composite unique index (rack_code + category)
        try {
            DB::statement("ALTER TABLE storage_racks ADD UNIQUE INDEX storage_racks_rack_code_category_unique (rack_code, category)");
        } catch (\Exception $e) { /* Ignore */ }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop Composite Unique
        try {
            DB::statement("ALTER TABLE storage_racks DROP INDEX storage_racks_rack_code_category_unique");
        } catch (\Exception $e) { /* Ignore */ }

        // 2. Restore Global Unique
        try {
            DB::statement("ALTER TABLE storage_racks ADD UNIQUE INDEX storage_racks_rack_code_unique (rack_code)");
        } catch (\Exception $e) { /* Ignore */ }

        // 3. Restore Foreign Key (Only if possible - data integrity might fail if duplicates exist)
        try {
             DB::statement("ALTER TABLE storage_assignments ADD CONSTRAINT storage_assignments_rack_code_foreign FOREIGN KEY (rack_code) REFERENCES storage_racks(rack_code) ON DELETE RESTRICT");
        } catch (\Exception $e) { /* Ignore */ }
    }
};
