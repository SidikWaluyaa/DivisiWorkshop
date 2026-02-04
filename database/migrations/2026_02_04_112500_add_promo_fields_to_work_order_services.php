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
        Schema::table('work_order_services', function (Blueprint $table) {
            $table->unsignedBigInteger('promotion_id')->nullable()->after('cost');
            $table->decimal('original_cost', 12, 2)->nullable()->after('promotion_id')->comment('Cost before discount');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('original_cost')->comment('Discount amount from promo');
            
            // Foreign Key
            $table->foreign('promotion_id')
                  ->references('id')
                  ->on('promotions')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order_services', function (Blueprint $table) {
            $table->dropForeign(['promotion_id']);
            $table->dropColumn(['promotion_id', 'original_cost', 'discount_amount']);
        });
    }
};
