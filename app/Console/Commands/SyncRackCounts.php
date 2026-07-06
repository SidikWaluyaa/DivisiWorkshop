<?php

namespace App\Console\Commands;

use App\Models\StorageRack;
use App\Models\StorageAssignment;
use Illuminate\Console\Command;

class SyncRackCounts extends Command
{
    protected $signature = 'storage:sync-counts';
    protected $description = 'Synchronize storage rack current_count with actual assignment data';

    public function handle()
    {
        $this->info('Syncing rack counts...');
        
        $racks = StorageRack::all();
        $fixed = 0;
        
        foreach ($racks as $rack) {
            // Use rack_code instead of rack_id
            $actualCount = StorageAssignment::where('rack_code', $rack->rack_code)
                ->where('category', $rack->category)
                ->where('status', 'stored')
                ->whereNull('retrieved_at')
                ->when($rack->category === 'before', function($q) {
                    $q->whereHas('workOrder', function($wq) {
                        $wq->whereIn('status', [
                            'DITERIMA',
                            'ASSESSMENT',
                            'READY_TO_DISPATCH',
                            'WAITING_PAYMENT',
                            'OTW_WORKSHOP',
                            'CX_FOLLOWUP',
                            'SPK_PENDING'
                        ]);
                    });
                }, function($q) {
                    $q->whereHas('workOrder');
                })
                ->count();
            
            if ($rack->current_count !== $actualCount) {
                $this->line("Rack {$rack->rack_code} ({$rack->category->value}): {$rack->current_count} → {$actualCount}");
                $rack->update(['current_count' => $actualCount]);
                $fixed++;
            }
        }
        
        if ($fixed > 0) {
            $this->info("✓ Fixed {$fixed} rack(s)");
        } else {
            $this->info("✓ All racks already synced");
        }
        
        return self::SUCCESS;
    }
}
