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
        // 1. CS Activities
        Schema::table('cs_activities', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable()->change()->constrained('users')->nullOnDelete();
        });

        // 2. Order Payments
        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropForeign(['pic_id']);
            $table->foreignId('pic_id')->nullable()->change()->constrained('users')->nullOnDelete();
        });

        // 3. Workshop Manifests
        Schema::table('workshop_manifests', function (Blueprint $table) {
            $table->dropForeign(['dispatcher_id']);
            $table->dropForeign(['receiver_id']);
            
            $table->foreignId('dispatcher_id')->nullable()->change()->constrained('users')->nullOnDelete();
            $table->foreignId('receiver_id')->nullable()->change()->constrained('users')->nullOnDelete();
        });

        // 4. CS Leads
        Schema::table('cs_leads', function (Blueprint $table) {
            $table->dropForeign(['pic_id']);
            $table->foreignId('pic_id')->nullable()->change()->constrained('users')->nullOnDelete();
        });
        
        // 5. Work Orders (PIC Columns)
        Schema::table('work_orders', function (Blueprint $table) {
            // Only drop if they exist and are restricted
            // In Laravel migrations, it's safer to just re-apply nullOnDelete 
            // but we need to drop the old one first. 
            // PIC Finance
            $table->dropForeign(['pic_finance_id']);
            $table->foreignId('pic_finance_id')->nullable()->change()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not specifically needed as we are fixing a blocking issue, 
        // but for completeness, we could restore restricted constraints.
    }
};
