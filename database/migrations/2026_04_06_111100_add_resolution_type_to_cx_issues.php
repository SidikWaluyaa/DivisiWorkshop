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
        Schema::table('cx_issues', function (Blueprint $table) {
            $table->string('resolution_type')->nullable()->after('resolved_at')->index();
        });

        // Perform Backfill for April 2026 data
        $issues = \App\Models\CxIssue::where('status', 'RESOLVED')
            ->whereBetween('resolved_at', ['2026-04-01 00:00:00', now()])
            ->get();

        foreach ($issues as $issue) {
            $type = 'lanjut'; // Default

            // 1. Check for cancel status
            if ($issue->workOrder && $issue->workOrder->status === \App\Enums\WorkOrderStatus::BATAL->value) {
                $type = 'cancel';
            } 
            // 2. Check for extra services added on/near resolution date
            else {
                $hasNewServices = \DB::table('work_order_services')
                    ->where('work_order_id', $issue->work_order_id)
                    ->whereDate('created_at', $issue->resolved_at->toDateString())
                    ->exists();
                
                if ($hasNewServices) {
                    $type = 'tambah_jasa';
                }
            }

            $issue->update(['resolution_type' => $type]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cx_issues', function (Blueprint $table) {
            $table->dropColumn('resolution_type');
        });
    }
};
