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
        Schema::table('work_orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('finished_date');
            $table->index('taken_date');
            $table->index('spk_number'); // Useful for search
            $table->index('customer_phone'); // Useful for search
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['finished_date']);
            $table->dropIndex(['taken_date']);
            $table->dropIndex(['spk_number']);
            $table->dropIndex(['customer_phone']);
        });
    }
};
