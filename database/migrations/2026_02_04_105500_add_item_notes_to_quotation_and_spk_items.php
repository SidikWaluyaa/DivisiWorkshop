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
        // Add item_notes to cs_quotation_items
        if (!Schema::hasColumn('cs_quotation_items', 'item_notes')) {
            Schema::table('cs_quotation_items', function (Blueprint $table) {
                $table->text('item_notes')->nullable();
            });
        }

        // Add item_notes to cs_spk_items
        if (!Schema::hasColumn('cs_spk_items', 'item_notes')) {
            Schema::table('cs_spk_items', function (Blueprint $table) {
                $table->text('item_notes')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('cs_quotation_items', 'item_notes')) {
            Schema::table('cs_quotation_items', function (Blueprint $table) {
                $table->dropColumn('item_notes');
            });
        }

        if (Schema::hasColumn('cs_spk_items', 'item_notes')) {
            Schema::table('cs_spk_items', function (Blueprint $table) {
                $table->dropColumn('item_notes');
            });
        }
    }
};
