<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. CS Activities
        try {
            Schema::table('cs_activities', function (Blueprint $table) {
                $table->dropForeign('cs_activities_user_id_foreign');
            });
        } catch (\Exception $e) {}
        DB::statement("ALTER TABLE cs_activities MODIFY COLUMN user_id BIGINT UNSIGNED NULL");
        try {
            Schema::table('cs_activities', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Exception $e) {}

        // 2. Order Payments
        try {
            Schema::table('order_payments', function (Blueprint $table) {
                $table->dropForeign('order_payments_pic_id_foreign');
            });
        } catch (\Exception $e) {}
        DB::statement("ALTER TABLE order_payments MODIFY COLUMN pic_id BIGINT UNSIGNED NULL");
        try {
            Schema::table('order_payments', function (Blueprint $table) {
                $table->foreign('pic_id')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Exception $e) {}

        // 3. Workshop Manifests
        try {
            Schema::table('workshop_manifests', function (Blueprint $table) {
                $table->dropForeign('workshop_manifests_dispatcher_id_foreign');
                $table->dropForeign('workshop_manifests_receiver_id_foreign');
            });
        } catch (\Exception $e) {}
        DB::statement("ALTER TABLE workshop_manifests MODIFY COLUMN dispatcher_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE workshop_manifests MODIFY COLUMN receiver_id BIGINT UNSIGNED NULL");
        try {
            Schema::table('workshop_manifests', function (Blueprint $table) {
                $table->foreign('dispatcher_id')->references('id')->on('users')->nullOnDelete();
                $table->foreign('receiver_id')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Exception $e) {}

        // 4. CS Leads
        try {
            Schema::table('cs_leads', function (Blueprint $table) {
                $table->dropForeign('cs_leads_cs_id_foreign');
            });
        } catch (\Exception $e) {}
        DB::statement("ALTER TABLE cs_leads MODIFY COLUMN cs_id BIGINT UNSIGNED NULL");
        try {
            Schema::table('cs_leads', function (Blueprint $table) {
                $table->foreign('cs_id')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Exception $e) {}

        // 5. Work Orders (pic_finance_id)
        try {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->dropForeign('work_orders_pic_finance_id_foreign');
            });
        } catch (\Exception $e) {}
        DB::statement("ALTER TABLE work_orders MODIFY COLUMN pic_finance_id BIGINT UNSIGNED NULL");
        try {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->foreign('pic_finance_id')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Exception $e) {}

        // 6. CX Issues
        try {
            Schema::table('cx_issues', function (Blueprint $table) {
                $table->dropForeign('cx_issues_reported_by_foreign');
                $table->dropForeign('cx_issues_resolved_by_foreign');
            });
        } catch (\Exception $e) {}
        DB::statement("ALTER TABLE cx_issues MODIFY COLUMN reported_by BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE cx_issues MODIFY COLUMN resolved_by BIGINT UNSIGNED NULL");
        try {
            Schema::table('cx_issues', function (Blueprint $table) {
                $table->foreign('reported_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('resolved_by')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
