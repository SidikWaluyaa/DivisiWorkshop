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
        // Add manual_tl, manual_tn, manual_l
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE storage_racks MODIFY COLUMN category ENUM('shoes', 'accessories', 'before', 'manual', 'manual_tl', 'manual_tn', 'manual_l') DEFAULT 'shoes'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous state
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE storage_racks MODIFY COLUMN category ENUM('shoes', 'accessories', 'before', 'manual') DEFAULT 'shoes'");
    }
};
