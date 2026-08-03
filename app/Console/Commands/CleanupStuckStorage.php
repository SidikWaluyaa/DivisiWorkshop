<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StorageAssignment;
use App\Models\WorkOrder;
use App\Services\Storage\StorageService;

class CleanupStuckStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:cleanup-stuck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan barang-barang (SPK) yang sudah diambil/history tetapi statusnya masih nyangkut di rak.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mencari barang yang nyangkut di rak (SPK sudah diambil tapi status masih stored)...');

        $stuckAssignments = StorageAssignment::where('status', 'stored')
            ->whereHas('workOrder', function ($query) {
                $query->whereNotNull('taken_date');
            })
            ->get();

        if ($stuckAssignments->isEmpty()) {
            $this->info('Mantap! Tidak ada barang yang nyangkut di rak.');
            return;
        }

        $count = $stuckAssignments->count();
        $this->warn("Ditemukan {$count} barang nyangkut di rak. Memulai pembersihan otomatis...");

        $storageService = app(StorageService::class);
        $rackCodesToUpdate = [];

        foreach ($stuckAssignments as $assignment) {
            $assignment->update([
                'status' => 'retrieved',
                'retrieved_at' => now(),
                'notes' => $assignment->notes . "\n[AUTO-CLEANUP] Dikeluarkan paksa karena SPK sudah berstatus HISTORY/DIAMBIL."
            ]);

            if ($assignment->rack_code) {
                $rackCodesToUpdate[] = $assignment->rack_code;
            }

            // Bersihkan flag di work order
            if ($assignment->workOrder) {
                $assignment->workOrder->update([
                    'storage_rack_code' => null,
                    'stored_at' => null
                ]);
            }
        }

        // Recalculate unique racks
        $uniqueRacks = array_unique($rackCodesToUpdate);
        foreach ($uniqueRacks as $rack) {
            $storageService->recalculateRackCount($rack);
        }

        $this->info("✅ Berhasil membersihkan {$count} barang yang nyangkut dari rak!");
    }
}
