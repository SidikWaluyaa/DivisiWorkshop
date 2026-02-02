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
        // Add 'manual' to storage_racks.category
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE storage_racks MODIFY COLUMN category ENUM('shoes', 'accessories', 'before', 'manual') DEFAULT 'shoes'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE storage_racks MODIFY COLUMN category ENUM('shoes', 'accessories', 'before') DEFAULT 'shoes'");
    }
};
