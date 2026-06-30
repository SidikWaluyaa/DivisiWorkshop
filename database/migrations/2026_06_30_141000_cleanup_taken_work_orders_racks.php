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
        // 1. Find all completed and taken work orders that still have a storage rack code
        $workOrders = DB::table('work_orders')
            ->where('status', 'SELESAI')
            ->whereNotNull('taken_date')
            ->whereNotNull('storage_rack_code')
            ->get();

        if ($workOrders->isEmpty()) {
            return;
        }

        $rackCodesToRecalculate = [];

        foreach ($workOrders as $order) {
            $rackCodesToRecalculate[$order->storage_rack_code] = true;

            // 2. Set status of their active storage assignments to 'retrieved'
            DB::table('storage_assignments')
                ->where('work_order_id', $order->id)
                ->where('status', 'stored')
                ->update([
                    'status' => 'retrieved',
                    'retrieved_at' => $order->taken_date ?? now(),
                    'retrieved_by' => null, // Set null or system user
                    'notes' => DB::raw("CONCAT(COALESCE(notes, ''), '\n[SISTEM Migration] Pembersihan otomatis karena status sudah SELESAI & Diambil')")
                ]);
        }

        // 3. Update the work orders in a single query
        DB::table('work_orders')
            ->where('status', 'SELESAI')
            ->whereNotNull('taken_date')
            ->whereNotNull('storage_rack_code')
            ->update([
                'storage_rack_code' => null,
                'stored_at' => null,
            ]);

        // 4. Recalculate the count for affected racks
        foreach (array_keys($rackCodesToRecalculate) as $rackCode) {
            if (!$rackCode) continue;

            $racks = DB::table('storage_racks')
                ->where('rack_code', $rackCode)
                ->get();

            foreach ($racks as $rack) {
                $actualCount = DB::table('storage_assignments')
                    ->where('rack_code', $rackCode)
                    ->where('category', $rack->category)
                    ->where('status', 'stored')
                    ->count();

                DB::table('storage_racks')
                    ->where('id', $rack->id)
                    ->update(['current_count' => $actualCount]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback possible/needed for data cleanup migrations
    }
};
