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
        // MySQL specific: Update enum values
        DB::statement("ALTER TABLE cs_spk MODIFY COLUMN status ENUM('DRAFT', 'WAITING_DP', 'WAITING_VERIFICATION', 'DP_PAID', 'HANDED_TO_WORKSHOP') DEFAULT 'DRAFT'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE cs_spk MODIFY COLUMN status ENUM('DRAFT', 'WAITING_DP', 'DP_PAID', 'HANDED_TO_WORKSHOP') DEFAULT 'DRAFT'");
    }
};
