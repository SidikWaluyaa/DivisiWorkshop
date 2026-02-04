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
        // Use raw SQL to ensure compatibility and consistency with previous migrations
        DB::statement("ALTER TABLE cs_leads MODIFY COLUMN cs_id BIGINT UNSIGNED NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to NOT NULL (Note: this might fail if there are existing NULL rows)
        DB::statement("ALTER TABLE cs_leads MODIFY COLUMN cs_id BIGINT UNSIGNED NOT NULL");
    }
};
