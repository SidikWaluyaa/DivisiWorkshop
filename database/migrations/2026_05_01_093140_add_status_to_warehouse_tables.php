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
        Schema::table('warehouse_purchases', function (Blueprint $table) {
            $table->string('status')->default('PENDING')->after('purchase_type');
            // Statuses: PENDING, PROCESSING, COMPLETED, CANCELLED
        });

        Schema::table('warehouse_disbursements', function (Blueprint $table) {
            $table->string('status')->default('COMPLETED')->after('disbursement_number');
            // Default COMPLETED because usually disbursement is recorded after items are handed out
            // But we add status for consistency
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_purchases', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('warehouse_disbursements', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
