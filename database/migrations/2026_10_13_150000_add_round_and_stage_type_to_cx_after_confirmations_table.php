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
        Schema::table('cx_after_confirmations', function (Blueprint $table) {
            $table->unsignedTinyInteger('round')->default(1)->after('work_order_id')->index();
            $table->string('stage_type', 30)->default('REGULER')->after('round')->index();
        });

        // Backfill existing records with appropriate stage_type
        try {
            $records = DB::table('cx_after_confirmations')
                ->join('work_orders', 'cx_after_confirmations.work_order_id', '=', 'work_orders.id')
                ->select(
                    'cx_after_confirmations.id',
                    'work_orders.is_warranty',
                    'work_orders.has_active_oto',
                    'work_orders.is_revising',
                    'work_orders.priority'
                )
                ->get();

            foreach ($records as $rec) {
                $stage = 'REGULER';
                if (!empty($rec->is_warranty)) {
                    $stage = 'GARANSI';
                } elseif (in_array(strtoupper(trim((string)$rec->priority)), ['R&D', 'RND'])) {
                    $stage = 'RND';
                } elseif (!empty($rec->has_active_oto)) {
                    $stage = 'OTO';
                } elseif (!empty($rec->is_revising)) {
                    $stage = 'REVISI';
                }

                DB::table('cx_after_confirmations')
                    ->where('id', $rec->id)
                    ->update([
                        'round' => 1,
                        'stage_type' => $stage,
                    ]);
            }
        } catch (\Throwable $e) {
            // Log or ignore if table is empty
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cx_after_confirmations', function (Blueprint $table) {
            $table->dropIndex(['round']);
            $table->dropIndex(['stage_type']);
            $table->dropColumn(['round', 'stage_type']);
        });
    }
};
