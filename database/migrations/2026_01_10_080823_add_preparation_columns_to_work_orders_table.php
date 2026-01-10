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
            // Preparation - Washing, Sol, Upper tracking
            $table->timestamp('prep_washing_completed_at')->nullable()->after('status');
            $table->unsignedBigInteger('prep_washing_by')->nullable()->after('prep_washing_completed_at');
            
            $table->timestamp('prep_sol_completed_at')->nullable()->after('prep_washing_by');
            $table->unsignedBigInteger('prep_sol_by')->nullable()->after('prep_sol_completed_at');
            
            $table->timestamp('prep_upper_completed_at')->nullable()->after('prep_sol_by');
            $table->unsignedBigInteger('prep_upper_by')->nullable()->after('prep_upper_completed_at');

            // Foreign keys
            $table->foreign('prep_washing_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('prep_sol_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('prep_upper_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['prep_washing_by']);
            $table->dropForeign(['prep_sol_by']);
            $table->dropForeign(['prep_upper_by']);
            
            $table->dropColumn([
                'prep_washing_completed_at', 'prep_washing_by',
                'prep_sol_completed_at', 'prep_sol_by',
                'prep_upper_completed_at', 'prep_upper_by'
            ]);
        });
    }
};
