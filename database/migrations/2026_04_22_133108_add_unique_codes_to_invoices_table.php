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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('target_dp_amount', 12, 2)->default(0)->after('discount');
            $table->integer('dp_unique_code')->nullable()->after('target_dp_amount');
            $table->integer('final_unique_code')->nullable()->after('dp_unique_code');
            $table->string('invoice_dp_url')->nullable()->after('invoice_akhir_url');
            $table->string('invoice_final_url')->nullable()->after('invoice_dp_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'target_dp_amount', 
                'dp_unique_code', 
                'final_unique_code', 
                'invoice_dp_url', 
                'invoice_final_url'
            ]);
        });
    }
};
