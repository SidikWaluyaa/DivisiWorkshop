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
        // Move SPK from Purchase Header to Items
        Schema::table('warehouse_purchases', function (Blueprint $table) {
            $table->dropColumn('spk_number');
        });

        Schema::table('warehouse_purchase_items', function (Blueprint $table) {
            $table->string('spk_number')->nullable()->after('material_id')->index();
        });

        // Move SPK from Disbursement Header to Items
        Schema::table('warehouse_disbursements', function (Blueprint $table) {
            $table->dropColumn('spk_number');
        });

        Schema::table('warehouse_disbursement_items', function (Blueprint $table) {
            $table->string('spk_number')->nullable()->after('material_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_purchases', function (Blueprint $table) {
            $table->string('spk_number')->nullable()->index();
        });

        Schema::table('warehouse_purchase_items', function (Blueprint $table) {
            $table->dropColumn('spk_number');
        });

        Schema::table('warehouse_disbursements', function (Blueprint $table) {
            $table->string('spk_number')->nullable()->index();
        });

        Schema::table('warehouse_disbursement_items', function (Blueprint $table) {
            $table->dropColumn('spk_number');
        });
    }
};
