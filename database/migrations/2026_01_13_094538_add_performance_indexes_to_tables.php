<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add indexes to frequently queried columns for better performance.
     * Expected improvement: 10-100x faster queries on large datasets.
     */
    public function up(): void
    {
        // Work Orders - Most critical table
        Schema::table('work_orders', function (Blueprint $table) {
            // Single column indexes
            $table->index('status', 'idx_work_orders_status');
            $table->index('entry_date', 'idx_work_orders_entry_date');
            $table->index('estimation_date', 'idx_work_orders_estimation_date');
            $table->index('finished_date', 'idx_work_orders_finished_date');
            $table->index('customer_phone', 'idx_work_orders_customer_phone');
            $table->index('spk_number', 'idx_work_orders_spk_number');
            $table->index('current_location', 'idx_work_orders_current_location');
            
            // Composite indexes for common query patterns
            $table->index(['status', 'entry_date'], 'idx_work_orders_status_entry');
            $table->index(['status', 'estimation_date'], 'idx_work_orders_status_estimation');
            $table->index(['status', 'updated_at'], 'idx_work_orders_status_updated');
        });
        
        // Work Order Services - Pivot table
        Schema::table('work_order_services', function (Blueprint $table) {
            $table->index('work_order_id', 'idx_wos_work_order_id');
            $table->index('service_id', 'idx_wos_service_id');
            $table->index(['work_order_id', 'service_id'], 'idx_wos_composite');
        });
        
        // Work Order Materials - Pivot table
        Schema::table('work_order_materials', function (Blueprint $table) {
            $table->index('work_order_id', 'idx_wom_work_order_id');
            $table->index('material_id', 'idx_wom_material_id');
            $table->index('status', 'idx_wom_status');
            $table->index(['work_order_id', 'status'], 'idx_wom_composite');
        });
        
        // Logs - Heavy read table
        Schema::table('logs', function (Blueprint $table) {
            $table->index('work_order_id', 'idx_logs_work_order_id');
            $table->index('step', 'idx_logs_step');
            $table->index('action', 'idx_logs_action');
            $table->index('created_at', 'idx_logs_created_at');
            $table->index(['work_order_id', 'created_at'], 'idx_logs_composite');
            $table->index(['work_order_id', 'step'], 'idx_logs_wo_step');
        });
        
        // Materials - Frequently queried
        Schema::table('materials', function (Blueprint $table) {
            $table->index('type', 'idx_materials_type');
            $table->index('sub_category', 'idx_materials_sub_category');
            $table->index('stock', 'idx_materials_stock');
            $table->index(['type', 'sub_category'], 'idx_materials_type_subcat');
        });
        
        // Users - For technician queries
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'idx_users_role');
            $table->index('specialization', 'idx_users_specialization');
            $table->index(['role', 'specialization'], 'idx_users_role_spec');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropIndex('idx_work_orders_status');
            $table->dropIndex('idx_work_orders_entry_date');
            $table->dropIndex('idx_work_orders_estimation_date');
            $table->dropIndex('idx_work_orders_finished_date');
            $table->dropIndex('idx_work_orders_customer_phone');
            $table->dropIndex('idx_work_orders_spk_number');
            $table->dropIndex('idx_work_orders_current_location');
            $table->dropIndex('idx_work_orders_status_entry');
            $table->dropIndex('idx_work_orders_status_estimation');
            $table->dropIndex('idx_work_orders_status_updated');
        });
        
        Schema::table('work_order_services', function (Blueprint $table) {
            $table->dropIndex('idx_wos_work_order_id');
            $table->dropIndex('idx_wos_service_id');
            $table->dropIndex('idx_wos_composite');
        });
        
        Schema::table('work_order_materials', function (Blueprint $table) {
            $table->dropIndex('idx_wom_work_order_id');
            $table->dropIndex('idx_wom_material_id');
            $table->dropIndex('idx_wom_status');
            $table->dropIndex('idx_wom_composite');
        });
        
        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex('idx_logs_work_order_id');
            $table->dropIndex('idx_logs_step');
            $table->dropIndex('idx_logs_action');
            $table->dropIndex('idx_logs_created_at');
            $table->dropIndex('idx_logs_composite');
            $table->dropIndex('idx_logs_wo_step');
        });
        
        Schema::table('materials', function (Blueprint $table) {
            $table->dropIndex('idx_materials_type');
            $table->dropIndex('idx_materials_sub_category');
            $table->dropIndex('idx_materials_stock');
            $table->dropIndex('idx_materials_type_subcat');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_specialization');
            $table->dropIndex('idx_users_role_spec');
        });
    }
};
