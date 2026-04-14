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
        Schema::table('material_request_items', function (Blueprint $table) {
            $table->foreignId('work_order_id')->nullable()->after('material_request_id')->constrained()->nullOnDelete();
        });

        Schema::table('material_requests', function (Blueprint $table) {
            $table->foreignId('work_order_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_requests', function (Blueprint $table) {
            $table->foreignId('work_order_id')->nullable(false)->change();
        });

        Schema::table('material_request_items', function (Blueprint $table) {
            $table->dropForeign(['work_order_id']);
            $table->dropColumn('work_order_id');
        });
    }
};
