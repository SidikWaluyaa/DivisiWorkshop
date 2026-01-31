<?php

namespace App\Services\Storage;

use App\Models\WorkOrder;
use App\Models\StorageRack;
use App\Models\StorageAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\WorkOrderStatus;

class StorageService
{
    /**
     * Auto-assign work order to best available rack
     */
    public function autoAssignRack(?string $locationPreference = null): ?StorageRack
    {
        $query = StorageRack::active()->available();

        if ($locationPreference) {
            $query->byLocation($locationPreference);
        }

        // Get rack with lowest utilization (prefer emptier racks)
        return $query->orderBy('current_count', 'asc')->first();
    }

    /**
     * Assign work order to specific rack
     */
    public function assignToRack(int $workOrderId, string $rackCode, ?string $notes = null, ?string $category = null): StorageAssignment
    {
        return DB::transaction(function () use ($workOrderId, $rackCode, $notes, $category) {
            $workOrder = WorkOrder::findOrFail($workOrderId);
            
            // Resolve Rack (check category if provided, otherwise find first or fail)
            $rackQuery = StorageRack::where('rack_code', $rackCode);
            if ($category) {
                $rackQuery->where('category', $category);
            }
            $rack = $rackQuery->firstOrFail();
            
            // Validation: unique check implied by firstOrFail based on criteria
            $category = $category ?? $rack->category;

            // Check if rack is available
            if (!$rack->isAvailable()) {
                throw new \Exception("Rack {$rackCode} ({$rack->category}) is full or inactive");
            }

            // Check if work order already stored
            if ($workOrder->storage_rack_code && $workOrder->storage_rack_code !== $rackCode) {
                 // Warning: strictly this might block moving items. For now we assume new assignment.
            }

            $now = now();

            // Create storage assignment
            $assignment = StorageAssignment::create([
                'work_order_id' => $workOrderId,
                'rack_code' => $rackCode,
                'category' => $category,
                'stored_at' => $now,
                'stored_by' => Auth::id(),
                'status' => 'stored',
                'notes' => $notes,
            ]);

            // Update work order
            $workOrder->update([
                'storage_rack_code' => $rackCode,
                'stored_at' => $now,
            ]);

            // Recalculate rack count
            $this->recalculateRackCount($rackCode, $category);

            return $assignment;
        });
    }

    /**
     * Retrieve work order from storage
     */
    public function retrieveFromStorage(int $workOrderId, ?string $notes = null): StorageAssignment
    {
        return DB::transaction(function () use ($workOrderId, $notes) {
            $workOrder = WorkOrder::findOrFail($workOrderId);

            if (!$workOrder->storage_rack_code) {
                throw new \Exception("Work order is not in storage");
            }

            // Get active assignment
            $assignment = StorageAssignment::where('work_order_id', $workOrderId)
                ->where('status', 'stored')
                ->firstOrFail();

            $now = now();

            // Update assignment
            $assignment->update([
                'retrieved_at' => $now,
                'retrieved_by' => Auth::id(),
                'status' => 'retrieved',
                'notes' => $assignment->notes . ($notes ? "\nRetrieved: {$notes}" : ''),
            ]);

            // Update work order
            // CRITICAL: Only set taken_date if it's NOT from 'before' rack (start of process vs end of process)
            // If category is Inbound/Before, we assume it's moving to Workshop, not Taken by Customer
            $isStartOfProcess = in_array($assignment->category, ['before', 'Inbound']) || ($workOrder->status === WorkOrderStatus::DITERIMA);

            $workOrder->update([
                'retrieved_at' => $now,
                'taken_date' => $isStartOfProcess ? null : ($workOrder->taken_date ?? $now), 
            ]);

            // Recalculate rack count
            $rackCode = $workOrder->storage_rack_code;
            if ($rackCode) {
                $this->recalculateRackCount($rackCode);
            }

            return $assignment;
        });
    }

    /**
     * Release item from Inbound Rack (Internal Move)
     * Does NOT set taken_date
     */
    public function releaseFromInbound(WorkOrder $workOrder)
    {
        return DB::transaction(function () use ($workOrder) {
            // Find active assignment in 'before' category
            $assignment = StorageAssignment::where('work_order_id', $workOrder->id)
                ->where('status', 'stored')
                ->whereIn('category', ['before', 'Inbound'])
                ->first();

            if ($assignment) {
                $now = now();
                
                // Update Assignment
                $assignment->update([
                    'retrieved_at' => $now,
                    'retrieved_by' => Auth::id(),
                    'status' => 'retrieved',
                    'notes' => $assignment->notes . "\nAuto-released to Preparation",
                ]);

                // Update Work Order
                $workOrder->update([
                    'storage_rack_code' => null,
                    'stored_at' => null,
                    'retrieved_at' => $now, // Mark retrieved from storage
                ]);

                // Recalculate Rack
                if ($assignment->rack_code) {
                    $this->recalculateRackCount($assignment->rack_code);
                }
            }
        });
    }

    /**
     * Unassign from storage (Undo storage)
     */
    public function unassignFromRack(int $workOrderId)
    {
        return DB::transaction(function () use ($workOrderId) {
            $workOrder = WorkOrder::findOrFail($workOrderId);

            // Decrement rack count (Old Logic - removed in favor of recalculation)
            /*
            if ($workOrder->storage_rack_code) {
                 $rack = StorageRack::where('rack_code', $workOrder->storage_rack_code)->first();
                 if ($rack) {
                     $rack->decrementCount();
                 }
            }
            */
            
            $rackCode = $workOrder->storage_rack_code;

            // Get ALL active assignments (in case of duplicates)
            $assignments = StorageAssignment::where('work_order_id', $workOrderId)
                ->where('status', 'stored')
                ->get();

            // Delete duplicates if any
            /** @var StorageAssignment $assignment */
            foreach ($assignments as $assignment) {
                // If assignment has different rack code (weird edge case), track it too
                if ($assignment->rack_code !== $rackCode && $assignment->rack_code) {
                    // We need to clean up that rack too if it differs
                    $otherRack = $assignment->rack_code;
                    $assignment->delete();
                    $this->recalculateRackCount($otherRack);
                } else {
                    $assignment->delete(); 
                }
            }

            // Update work order
            $workOrder->update([
                'storage_rack_code' => null,
                'stored_at' => null,
                'retrieved_at' => null,
            ]);

            // Recalculate count for the original rack to ensure it's correct
            if ($rackCode) {
                $this->recalculateRackCount($rackCode);
            }

            return true;
        });
    }

    /**
     * Recalculate and update rack count based on actual active assignments
     */
    private function recalculateRackCount(string $rackCode, $category = null)
    {
        // Extract string value if Enum
        $categoryValue = $category instanceof \App\Enums\StorageCategory 
            ? $category->value 
            : $category;

        $racks = StorageRack::where('rack_code', $rackCode)
             ->when($categoryValue, fn($q) => $q->where('category', $categoryValue))
             ->get();

        foreach ($racks as $rack) {
            $actualCount = StorageAssignment::where('rack_code', $rackCode)
                ->where('category', $rack->category)
                ->where('status', 'stored')
                ->count();
            
            $rack->update(['current_count' => $actualCount]);
        }
    }

    /**
     * Get available racks
     */
    public function getAvailableRacks(?string $location = null)
    {
        $query = StorageRack::active()->available();

        if ($location) {
            $query->byLocation($location);
        }

        return $query->orderBy('current_count', 'asc')->get();
    }

    /**
     * Get rack utilization statistics
     */
    public function getRackUtilization(?string $category = null): array
    {
        $query = StorageRack::active();
        
        if ($category && in_array($category, ['shoes', 'accessories', 'before'])) {
            $query->where('category', $category);
        }

        $racks = $query->get();

        $totalCapacity = $racks->sum('capacity');
        $totalUsed = $racks->sum('current_count');
        $utilizationPercentage = $totalCapacity > 0 ? ($totalUsed / $totalCapacity) * 100 : 0;

        return [
            'total_racks' => $racks->count(),
            'total_capacity' => $totalCapacity,
            'total_used' => $totalUsed,
            'total_available' => $totalCapacity - $totalUsed,
            'utilization_percentage' => round($utilizationPercentage, 2),
            'full_racks' => $racks->filter(fn($r) => $r->current_count >= $r->capacity)->count(),
            'empty_racks' => $racks->filter(fn($r) => $r->current_count === 0)->count(),
        ];
    }

    /**
     * Get overdue items (stored > X days)
     */
    public function getOverdueItems(int $days = 7, ?string $category = null)
    {
        return StorageAssignment::with(['workOrder.customer', 'rack'])
            ->when($category, function($q) use ($category) {
                 $q->whereHas('rack', function($rq) use ($category) {
                     $rq->where('category', $category);
                  });
             })
             ->overdue($days)
             ->orderBy('stored_at', 'asc')
             ->get();
     }
 
     /**
      * Get storage statistics
      */
     public function getStatistics(?string $category = null): array
     {
         $baseQuery = StorageAssignment::query();
         
         if ($category) {
             $baseQuery->whereHas('rack', function($q) use ($category) {
                 $q->where('category', $category);
             });
         }
 
         $totalStored = (clone $baseQuery)->stored()->count();
         $totalRetrieved = (clone $baseQuery)->isRetrieved()->count();
         $overdueCount = (clone $baseQuery)->overdue(7)->count();
 
         // Average storage duration for retrieved items
         $avgDuration = (clone $baseQuery)->isRetrieved()
             ->selectRaw('AVG(TIMESTAMPDIFF(DAY, stored_at, retrieved_at)) as avg_days')
             ->value('avg_days');
 
         return [
             'total_stored' => $totalStored,
             'total_retrieved' => $totalRetrieved,
             'overdue_count' => $overdueCount,
             'avg_storage_days' => round($avgDuration ?? 0, 1),
         ];
     }
 
     /**
      * Search stored items
      */
     public function search(string $query, ?string $category = null)
     {
         return StorageAssignment::with(['workOrder.customer', 'rack'])
             ->stored()
             ->when($category, function($q) use ($category) {
                 // Ensure we respect the category, prioritizing the column if populated
                 $q->where(function($sub) use ($category) {
                     $sub->where(function($strict) use ($category) {
                         // If category column is set, it MUST match the requested category
                         $strict->whereNotNull('category')->where('category', $category);
                     })
                     ->orWhere(function($fallback) use ($category) {
                         // Only fall back to rack relationship if category column is null
                         $fallback->whereNull('category')->whereHas('rack', function($r) use ($category) {
                             $r->where('category', $category);
                         });
                     });
                 });
             })
             ->where(function ($q) use ($query) {
                 $q->whereHas('workOrder', function ($subQ) use ($query) {
                     $subQ->where('spk_number', 'like', "%{$query}%")
                         ->orWhereHas('customer', function ($cq) use ($query) {
                             $cq->where('name', 'like', "%{$query}%")
                                 ->orWhere('phone', 'like', "%{$query}%");
                         });
                 })
                 ->orWhere('rack_code', 'like', "%{$query}%");
             })
             ->orderBy('stored_at', 'desc')
             ->get();
     }
 }
