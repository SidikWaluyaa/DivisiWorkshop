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
        Schema::table('storage_assignments', function (Blueprint $table) {
            // Add category column, nullable initially to support existing data
            $table->enum('category', ['shoes', 'accessories', 'before'])->nullable()->after('rack_code');
            
            // Add index for performance
            $table->index('category');
        });

        // Optional: Backfill logic could go here or in a seeder, 
        // but since we can't easily distinguish 'shoes' vs 'before' for existing 'A-01' without more context,
        // we might leave them null or default to 'shoes' based on item_type if available.
        // For now, nullable is safe.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storage_assignments', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
