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
        Schema::table('order_payments', function (Blueprint $table) {
            // Invoice snapshots for audit and reporting
            $table->text('services_snapshot')->nullable()->after('proof_image');
            $table->string('customer_name_snapshot')->nullable()->after('services_snapshot');
            $table->string('customer_phone_snapshot')->nullable()->after('customer_name_snapshot');
            $table->integer('total_bill_snapshot')->nullable()->after('customer_phone_snapshot');
            $table->integer('discount_snapshot')->nullable()->after('total_bill_snapshot');
            $table->integer('shipping_cost_snapshot')->nullable()->after('discount_snapshot');
            $table->integer('balance_snapshot')->nullable()->after('shipping_cost_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropColumn([
                'services_snapshot',
                'customer_name_snapshot',
                'customer_phone_snapshot',
                'total_bill_snapshot',
                'discount_snapshot',
                'shipping_cost_snapshot',
                'balance_snapshot'
            ]);
        });
    }
};
