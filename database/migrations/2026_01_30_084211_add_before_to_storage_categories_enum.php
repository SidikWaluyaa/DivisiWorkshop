<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'before' to storage_racks.category
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE storage_racks MODIFY COLUMN category ENUM('shoes', 'accessories', 'before') DEFAULT 'shoes'");
        
        // Add 'before' to storage_assignments.item_type
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE storage_assignments MODIFY COLUMN item_type ENUM('shoes', 'accessories', 'before') DEFAULT 'shoes'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM values
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE storage_racks MODIFY COLUMN category ENUM('shoes', 'accessories') DEFAULT 'shoes'");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE storage_assignments MODIFY COLUMN item_type ENUM('shoes', 'accessories') DEFAULT 'shoes'");
    }
};
