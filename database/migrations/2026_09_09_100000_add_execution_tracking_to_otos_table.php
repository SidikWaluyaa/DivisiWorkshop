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
        Schema::table('otos', function (Blueprint $table) {
            // Sol Station Tracking
            $table->foreignId('oto_sol_by')->nullable()->after('completed_at')->constrained('users')->onDelete('set null');
            $table->timestamp('oto_sol_started_at')->nullable()->after('oto_sol_by');
            $table->timestamp('oto_sol_completed_at')->nullable()->after('oto_sol_started_at');

            // Upper Station Tracking
            $table->foreignId('oto_upper_by')->nullable()->after('oto_sol_completed_at')->constrained('users')->onDelete('set null');
            $table->timestamp('oto_upper_started_at')->nullable()->after('oto_upper_by');
            $table->timestamp('oto_upper_completed_at')->nullable()->after('oto_upper_started_at');

            // Treatment / Cleaning / Repaint Station Tracking
            $table->foreignId('oto_treatment_by')->nullable()->after('oto_upper_completed_at')->constrained('users')->onDelete('set null');
            $table->timestamp('oto_treatment_started_at')->nullable()->after('oto_treatment_by');
            $table->timestamp('oto_treatment_completed_at')->nullable()->after('oto_treatment_started_at');

            // Final Completion PIC & Notes
            $table->foreignId('oto_completed_by')->nullable()->after('oto_treatment_completed_at')->constrained('users')->onDelete('set null');
            $table->text('oto_notes')->nullable()->after('oto_completed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otos', function (Blueprint $table) {
            $table->dropForeign(['oto_sol_by']);
            $table->dropForeign(['oto_upper_by']);
            $table->dropForeign(['oto_treatment_by']);
            $table->dropForeign(['oto_completed_by']);
            $table->dropColumn([
                'oto_sol_by', 'oto_sol_started_at', 'oto_sol_completed_at',
                'oto_upper_by', 'oto_upper_started_at', 'oto_upper_completed_at',
                'oto_treatment_by', 'oto_treatment_started_at', 'oto_treatment_completed_at',
                'oto_completed_by', 'oto_notes'
            ]);
        });
    }
};
