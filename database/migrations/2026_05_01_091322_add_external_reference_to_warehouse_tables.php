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
            $table->string('external_reference')->nullable()->after('purchase_number')->comment('Nomor Nota dari Vendor');
        });

        Schema::table('warehouse_disbursements', function (Blueprint $table) {
            $table->string('external_reference')->nullable()->after('disbursement_number')->comment('Nomor Referensi Manual/Memo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_purchases', function (Blueprint $table) {
            $table->dropColumn('external_reference');
        });

        Schema::table('warehouse_disbursements', function (Blueprint $table) {
            $table->dropColumn('external_reference');
        });
    }
};
