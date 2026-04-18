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
        Schema::table('material_transactions', function (Blueprint $table) {
            $table->integer('balance_after')->nullable()->after('quantity')->comment('Stock balance after this transaction');
            $table->decimal('unit_price', 15, 2)->nullable()->after('balance_after')->comment('Material price snapshot at the time of transaction');
            $table->decimal('total_value', 15, 2)->nullable()->after('unit_price')->comment('quantity * unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_transactions', function (Blueprint $table) {
            $table->dropColumn(['balance_after', 'unit_price', 'total_value']);
        });
    }
};
