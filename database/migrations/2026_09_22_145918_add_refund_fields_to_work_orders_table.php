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
            $table->decimal('refund_amount', 12, 2)->default(0)->after('reception_rejection_reason');
            $table->string('refund_notes', 500)->nullable()->after('refund_amount');
            $table->foreignId('refund_by')->nullable()->after('refund_notes')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['refund_by']);
            $table->dropColumn(['refund_amount', 'refund_notes', 'refund_by']);
        });
    }
};
