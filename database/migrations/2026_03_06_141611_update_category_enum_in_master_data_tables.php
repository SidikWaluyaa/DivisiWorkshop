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
        // Update Enum in master_issues table
        DB::statement("ALTER TABLE master_issues MODIFY category ENUM('TEKNIS', 'MATERIAL', 'KONFIRMASI') NOT NULL");
        
        // Update Enum in master_solutions table (keeping OVERLOAD and QC as they existed previously)
        DB::statement("ALTER TABLE master_solutions MODIFY category ENUM('TEKNIS', 'MATERIAL', 'OVERLOAD', 'QC', 'KONFIRMASI') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Enum in master_issues table
        DB::statement("ALTER TABLE master_issues MODIFY category ENUM('TEKNIS', 'MATERIAL') NOT NULL");
        
        // Revert Enum in master_solutions table
        DB::statement("ALTER TABLE master_solutions MODIFY category ENUM('TEKNIS', 'MATERIAL', 'OVERLOAD', 'QC') NOT NULL");
    }
};
