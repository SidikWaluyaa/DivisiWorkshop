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
        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->string('refund_bank_name', 100)->nullable()->after('notes');
            $table->string('refund_account_number', 100)->nullable()->after('refund_bank_name');
            $table->string('refund_account_name', 150)->nullable()->after('refund_account_number');
            $table->string('proof_image', 255)->nullable()->after('refund_account_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->dropColumn([
                'refund_bank_name',
                'refund_account_number',
                'refund_account_name',
                'proof_image',
            ]);
        });
    }
};
