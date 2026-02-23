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
        // Use raw SQL to avoid Laravel 11 schema introspection crash on older MySQL/MariaDB
        $columns = DB::select("SHOW COLUMNS FROM otos LIKE 'deleted_at'");
        
        if (empty($columns)) {
            DB::statement("ALTER TABLE otos ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = DB::select("SHOW COLUMNS FROM otos LIKE 'deleted_at'");
        
        if (!empty($columns)) {
            DB::statement("ALTER TABLE otos DROP COLUMN deleted_at");
        }
    }
};
