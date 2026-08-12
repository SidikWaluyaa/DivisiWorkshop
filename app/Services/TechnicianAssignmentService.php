<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TechnicianAssignmentService
{
    /**
     * Auto-assign prep (washing) technician evenly based on workload (FR-1.1)
     */
    public function autoAssignPrepWashing(WorkOrder $workOrder): ?User
    {
        if ($workOrder->prep_washing_by) {
            return User::find($workOrder->prep_washing_by);
        }

        // Find active technicians in 'sortir' pool specialized in Cuci/Washing or support
        $candidates = User::where('is_active', true)
            ->where(function ($q) {
                $q->where('role', 'technician')
                  ->orWhere('role', 'pic');
            })
            ->where(function ($q) {
                $q->where('workshop_pool', 'sortir')
                  ->orWhereNull('workshop_pool');
            })
            ->where(function ($q) {
                $q->where('specialization', 'like', '%Cuci%')
                  ->orWhere('specialization', 'like', '%Washing%')
                  ->orWhere('is_support', true)
                  ->orWhereNull('specialization');
            })
            ->get();

        if ($candidates->isEmpty()) {
            // Fallback to any active technician
            $candidates = User::where('role', 'technician')->where('is_active', true)->get();
        }

        if ($candidates->isEmpty()) {
            return null;
        }

        // Pick technician with lowest active prep washing workload
        $bestTech = $candidates->sortBy(function ($tech) {
            return WorkOrder::where('prep_washing_by', $tech->id)
                ->whereNull('prep_washing_completed_at')
                ->count();
        })->first();

        if ($bestTech) {
            $workOrder->update(['prep_washing_by' => $bestTech->id]);
            Log::info("Auto-assigned Prep Washing for SPK #{$workOrder->spk_number} to Technician: {$bestTech->name} (ID: {$bestTech->id})");
        }

        return $bestTech;
    }

    /**
     * Auto-assign technicians for all services in Production stage based on specialization & workload (FR-5.1)
     */
    public function autoAssignProductionTechnicians(WorkOrder $workOrder): void
    {
        $workOrder->loadMissing('workOrderServices.service');

        foreach ($workOrder->workOrderServices as $woService) {
            if ($woService->technician_id) {
                continue; // Skip if already manually assigned
            }

            $category = strtolower($woService->category_name ?? $woService->service?->category ?? '');
            $serviceName = strtolower($woService->service?->name ?? '');

            // Find matching technician
            $tech = $this->findBestAvailableTechnicianForCategory($category, $serviceName);

            if ($tech) {
                $woService->update(['technician_id' => $tech->id]);
                
                // Map to legacy WorkOrder columns for backward compatibility
                if (str_contains($category, 'sol')) {
                    $workOrder->update(['prod_sol_by' => $tech->id]);
                } elseif (str_contains($category, 'upper')) {
                    $workOrder->update(['prod_upper_by' => $tech->id]);
                } elseif (str_contains($category, 'clean') || str_contains($category, 'wash')) {
                    $workOrder->update(['prod_cleaning_by' => $tech->id]);
                } else {
                    $workOrder->update(['technician_production_id' => $tech->id]);
                }

                Log::info("Auto-assigned Production Service '{$woService->service?->name}' for SPK #{$workOrder->spk_number} to Technician: {$tech->name} (ID: {$tech->id})");
            }
        }
    }

    /**
     * Find best available technician for a given service category based on specialization and lowest workload
     */
    public function findBestAvailableTechnicianForCategory(string $category, string $serviceName = ''): ?User
    {
        // Category keyword mapping
        $keyword = 'General';
        if (str_contains($category, 'sol') || str_contains($serviceName, 'sol')) {
            $keyword = 'Soling';
        } elseif (str_contains($category, 'upper') || str_contains($serviceName, 'upper')) {
            $keyword = 'Upper';
        } elseif (str_contains($category, 'jahit') || str_contains($serviceName, 'jahit')) {
            $keyword = 'Jahit';
        } elseif (str_contains($category, 'repaint') || str_contains($serviceName, 'repaint')) {
            $keyword = 'Repaint';
        } elseif (str_contains($category, 'clean') || str_contains($category, 'wash')) {
            $keyword = 'Clean Up';
        }

        // Query candidate technicians
        $candidates = User::where('is_active', true)
            ->where(function ($q) {
                $q->where('role', 'technician')
                  ->orWhere('role', 'pic');
            })
            ->where(function ($q) use ($keyword) {
                $q->where('specialization', 'like', "%{$keyword}%")
                  ->orWhere('is_support', true)
                  ->orWhereNull('specialization');
            })
            ->get();

        if ($candidates->isEmpty()) {
            $candidates = User::where('role', 'technician')->where('is_active', true)->get();
        }

        if ($candidates->isEmpty()) {
            return null;
        }

        // Calculate workload and return technician with fewest active assigned services
        return $candidates->sortBy(function ($tech) {
            $workload = WorkOrderService::where('technician_id', $tech->id)
                ->where('status', '!=', 'completed')
                ->count();

            // Support technicians given slight priority if equal
            if ($tech->is_support) {
                $workload += 0.5;
            }

            return $workload;
        })->first();
    }

    /**
     * Get list of qualified technicians for a specific station/category (FR-5.8)
     */
    public function getQualifiedTechnicians(string $category)
    {
        $categoryLower = strtolower($category);

        return User::where('is_active', true)
            ->where(function ($q) {
                $q->where('role', 'technician')
                  ->orWhere('role', 'pic');
            })
            ->where(function ($q) use ($categoryLower) {
                $q->where('specialization', 'like', "%{$categoryLower}%")
                  ->orWhere('is_support', true)
                  ->orWhereNull('specialization');
            })
            ->select('id', 'name', 'specialization', 'workshop_pool', 'is_support')
            ->get();
    }

    /**
     * Get candidates for prep washing
     */
    public static function getPrepWashingCandidates()
    {
        $candidates = User::where('is_active', true)
            ->where(function ($q) {
                $q->where('role', 'technician')
                  ->orWhere('role', 'pic');
            })
            ->where(function ($q) {
                $q->where('workshop_pool', 'sortir')
                  ->orWhereNull('workshop_pool');
            })
            ->where(function ($q) {
                $q->where('specialization', 'like', '%Cuci%')
                  ->orWhere('specialization', 'like', '%Washing%')
                  ->orWhere('is_support', true)
                  ->orWhereNull('specialization');
            })
            ->get();

        if ($candidates->isEmpty()) {
            $candidates = User::where('role', 'technician')->where('is_active', true)->get();
        }
        return $candidates;
    }

    /**
     * Get recommended prep washing technician for an order based on lowest workload
     */
    public function getRecommendedPrepWashingTechnician(WorkOrder $workOrder, $candidates = null)
    {
        if (!$candidates) {
            $candidates = self::getPrepWashingCandidates();
        }
        if ($candidates->isEmpty()) {
            return null;
        }

        return $candidates->sortBy(function ($tech) {
            return WorkOrder::where('prep_washing_by', $tech->id)
                ->whereNull('prep_washing_completed_at')
                ->count();
        })->first();
    }
}
