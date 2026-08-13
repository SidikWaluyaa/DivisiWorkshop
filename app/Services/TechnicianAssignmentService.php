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

        $hasSol = $workOrder->workOrderServices->contains(fn($s) => \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'sol') || \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'sol'));
        $hasUpper = $workOrder->workOrderServices->contains(fn($s) => \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'upper') || \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'upper'));
        $hasTreatment = $workOrder->workOrderServices->contains(fn($s) => \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'clean') || \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'wash') || \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'treatment') || \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'repaint') || \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'whitening') || \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'clean') || \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'treatment'));

        if (!$hasSol && !$hasUpper) {
            $hasTreatment = true;
        }

        $updates = [];

        // 1. Soling (Only assign if station is required and currently NULL)
        if ($hasSol && !$workOrder->prod_sol_by) {
            $tech = $this->findBestAvailableTechnicianForCategory('sol', 'soling');
            if ($tech) {
                $updates['prod_sol_by'] = $tech->id;
                foreach ($workOrder->workOrderServices as $woService) {
                    $cat = strtolower($woService->category_name ?? $woService->service?->category ?? '');
                    $sName = strtolower($woService->service?->name ?? '');
                    if (str_contains($cat, 'sol') || str_contains($sName, 'sol')) {
                        $woService->update(['technician_id' => $tech->id]);
                    }
                }
                Log::info("Auto-assigned Soling for SPK #{$workOrder->spk_number} to Technician: {$tech->name} (ID: {$tech->id})");
            }
        }

        // 2. Upper (Only assign if station is required and currently NULL)
        if ($hasUpper && !$workOrder->prod_upper_by) {
            $tech = $this->findBestAvailableTechnicianForCategory('upper', 'upper');
            if ($tech) {
                $updates['prod_upper_by'] = $tech->id;
                foreach ($workOrder->workOrderServices as $woService) {
                    $cat = strtolower($woService->category_name ?? $woService->service?->category ?? '');
                    $sName = strtolower($woService->service?->name ?? '');
                    if (str_contains($cat, 'upper') || str_contains($cat, 'jahit') || str_contains($sName, 'upper') || str_contains($sName, 'jahit')) {
                        $woService->update(['technician_id' => $tech->id]);
                    }
                }
                Log::info("Auto-assigned Upper for SPK #{$workOrder->spk_number} to Technician: {$tech->name} (ID: {$tech->id})");
            }
        }

        // 3. Treatment (Only assign if station is required and currently NULL)
        if ($hasTreatment && !$workOrder->prod_cleaning_by) {
            $tech = $this->findBestAvailableTechnicianForCategory('clean', 'treatment');
            if ($tech) {
                $updates['prod_cleaning_by'] = $tech->id;
                foreach ($workOrder->workOrderServices as $woService) {
                    $woService->update(['technician_id' => $tech->id]);
                }
                Log::info("Auto-assigned Treatment for SPK #{$workOrder->spk_number} to Technician: {$tech->name} (ID: {$tech->id})");
            }
        }

        if (!empty($updates)) {
            $workOrder->update($updates);
        }
    }

    /**
     * Auto-assign technicians for all stations in QC stage based on specialization & workload.
     * Skips stations that already have a technician assigned.
     */
    public function autoAssignQcTechnicians(WorkOrder $workOrder): void
    {
        $updates = [];

        $workOrder->loadMissing('workOrderServices.service');
        $hasJahitQc = $workOrder->workOrderServices->contains(fn($s) => 
            \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'sol') || 
            \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'sol') ||
            \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'upper') || 
            \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'upper') ||
            \Illuminate\Support\Str::contains(strtolower($s->category_name ?? ''), 'jahit') || 
            \Illuminate\Support\Str::contains(strtolower($s->service?->name ?? ''), 'jahit')
        );

        // 1. QC Jahit (Only assign if required by services and currently NULL)
        if ($hasJahitQc && !$workOrder->qc_jahit_by) {
            $tech = $this->findBestAvailableTechnicianForCategory('jahit', 'jahit');
            if ($tech) {
                $updates['qc_jahit_by'] = $tech->id;
                Log::info("Auto-assigned QC Jahit for SPK #{$workOrder->spk_number} to Technician: {$tech->name} (ID: {$tech->id})");
            }
        }

        // 2. QC Cleanup (Only assign if currently NULL)
        if (!$workOrder->qc_cleanup_by) {
            $tech = $this->findBestAvailableTechnicianForCategory('clean', 'cleanup');
            if ($tech) {
                $updates['qc_cleanup_by'] = $tech->id;
                Log::info("Auto-assigned QC Cleanup for SPK #{$workOrder->spk_number} to Technician: {$tech->name} (ID: {$tech->id})");
            }
        }

        // 3. QC Final (Only assign if currently NULL)
        if (!$workOrder->qc_final_by) {
            $tech = $this->findBestAvailableTechnicianForCategory('qc', 'final');
            if ($tech) {
                $updates['qc_final_by'] = $tech->id;
                Log::info("Auto-assigned QC Final for SPK #{$workOrder->spk_number} to Technician: {$tech->name} (ID: {$tech->id})");
            }
        }

        if (!empty($updates)) {
            $workOrder->update($updates);
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
        } elseif (str_contains($category, 'clean') || str_contains($category, 'wash') || str_contains($category, 'treatment') || str_contains($category, 'whitening') || str_contains($serviceName, 'treatment') || str_contains($serviceName, 'clean')) {
            $keyword = 'Clean Up';
        } elseif (str_contains($category, 'qc') || str_contains($serviceName, 'qc') || str_contains($serviceName, 'final')) {
            $keyword = 'QC';
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
