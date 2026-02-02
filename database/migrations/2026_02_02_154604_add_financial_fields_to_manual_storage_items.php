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
        Schema::table('manual_storage_items', function (Blueprint $table) {
            $table->string('spk_number')->nullable()->after('item_name')->index();
            // tagih_lunas (TL), tagih_nanti (TN), lunas (L)
            $table->enum('payment_status', ['tagih_lunas', 'tagih_nanti', 'lunas'])->default('lunas')->after('status')->index();
            
            $table->decimal('total_price', 15, 2)->default(0)->after('payment_status');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manual_storage_items', function (Blueprint $table) {
            $table->dropColumn(['spk_number', 'payment_status', 'total_price', 'paid_amount']);
        });
    }
};
