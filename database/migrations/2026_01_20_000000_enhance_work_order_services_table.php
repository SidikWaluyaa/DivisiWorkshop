<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_order_services', function (Blueprint $table) {
            // 1. Support Custom Service: service_id can be null if custom name is provided
            $table->foreignId('service_id')->nullable()->change();
            
            // 2. Custom Service Name: For manual input services
            $table->string('custom_service_name')->nullable()->after('service_id');
            
            // 3. Category Snapshot: To store category name at time of order
            $table->string('category_name')->nullable()->after('custom_service_name');
            
            // 4. Multiple Details: JSON array for extra details (e.g. ["Extra Wangi", "Jahit Sol"])
            $table->longText('service_details')->nullable()->after('cost');
        });
    }

    public function down(): void
    {
        Schema::table('work_order_services', function (Blueprint $table) {
            // Revert changes
            $table->foreignId('service_id')->nullable(false)->change();
            $table->dropColumn(['custom_service_name', 'category_name', 'service_details']);
        });
    }
};
