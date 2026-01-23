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
            $table->string('storage_rack_code')->nullable()->after('qc_final_completed_at');
            $table->timestamp('stored_at')->nullable()->after('storage_rack_code');
            $table->timestamp('retrieved_at')->nullable()->after('stored_at');
            
            $table->index('storage_rack_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropIndex(['storage_rack_code']);
            $table->dropColumn(['storage_rack_code', 'stored_at', 'retrieved_at']);
        });
    }
};
