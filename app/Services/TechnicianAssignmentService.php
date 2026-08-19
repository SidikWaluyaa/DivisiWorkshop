<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderService;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\ProductionStationHelper;

class TechnicianAssignmentService
{
    /**
     * Excluded non-repair staff (PIC Material)
     */
    protected array $excludedSpecs = ['PIC Material Sol', 'PIC Material Upper', 'PIC Material', 'PIC MATERIAL SOL', 'PIC MATERIAL UPPER'];

    /**
     * Auto-assign prep (washing) technician evenly based on workload
     */
    public function autoAssignPrepWashing(WorkOrder $workOrder, bool $forceReassign = false): ?User
    {
        if ($workOrder->prep_washing_by && !$forceReassign && !$workOrder->prep_washing_started_at) {
            $existing = User::find($workOrder->prep_washing_by);
            if ($existing && !str_contains($existing->name, 'Dr. Shoe')) {
                return $existing;
            }
        }

        // Candidates: Active technicians with station = 'PREPARATION' or specialization Washing/Cuci
        $candidates = User::where('is_active', true)
            ->where('role', 'technician')
            ->where(function ($q) {
                $q->where('station', 'PREPARATION')
                  ->orWhere('specialization', 'like', '%Cuci%')
                  ->orWhere('specialization', 'like', '%Washing%');
            })
            ->whereNotIn('specialization', $this->excludedSpecs)
            ->where('name', 'not like', '%Dr. Shoe%')
            ->get();

        if ($candidates->isEmpty()) {
            $candidates = User::where('role', 'technician')
                ->where('is_active', true)
                ->whereNotIn('specialization', $this->excludedSpecs)
                ->where('name', 'not like', '%Dr. Shoe%')
                ->get();
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
     * Auto-assign technicians for all services in Production stage based on station & technician_services skill mapping
     */
    public function autoAssignProductionTechnicians(WorkOrder $workOrder, bool $forceReassign = false): void
    {
        $workOrder->loadMissing(['workOrderServices.service', 'prodSolBy', 'prodUpperBy', 'prodCleaningBy']);

        $hasSol = $workOrder->workOrderServices->contains(fn($s) => ProductionStationHelper::getStationCode($s->category_name ?? $s->service?->name ?? '') === 'SOLING');
        $hasUpper = $workOrder->workOrderServices->contains(fn($s) => ProductionStationHelper::getStationCode($s->category_name ?? $s->service?->name ?? '') === 'UPPER');
        $hasTreatment = $workOrder->workOrderServices->contains(fn($s) => ProductionStationHelper::getStationCode($s->category_name ?? $s->service?->name ?? '') === 'TREATMENT');

        if (!$hasSol && !$hasUpper) {
            $hasTreatment = true;
        }

        $updates = [];

        // Helper check for unstarted / placeholder tech
        $isDrShoeSol = str_contains($workOrder->prodSolBy?->name ?? '', 'Dr. Shoe');
        $isDrShoeUpper = str_contains($workOrder->prodUpperBy?->name ?? '', 'Dr. Shoe');
        $isDrShoeClean = str_contains($workOrder->prodCleaningBy?->name ?? '', 'Dr. Shoe');

        // 1. Soling Station
        if ($hasSol && (!$workOrder->prod_sol_by || $isDrShoeSol || (!$workOrder->prod_sol_started_at && $forceReassign))) {
            $solServices = $workOrder->workOrderServices->filter(fn($s) => ProductionStationHelper::getStationCode($s->category_name ?? $s->service?->name ?? '') === 'SOLING');
            $serviceIds = $solServices->pluck('service_id')->filter()->toArray();

            $tech = $this->findBestTechnicianForStationAndServices('SOLING', $serviceIds);
            if ($tech) {
                $updates['prod_sol_by'] = $tech->id;
                foreach ($solServices as $woService) {
                    if (!$woService->started_at) {
                        $woService->update(['technician_id' => $tech->id]);
                    }
                }
                Log::info("Auto-assigned Soling for SPK #{$workOrder->spk_number} to Technician: {$tech->name} (ID: {$tech->id})");
            }
        }

        // 2. Upper Station
        if ($hasUpper && (!$workOrder->prod_upper_by || $isDrShoeUpper || (!$workOrder->prod_upper_started_at && $forceReassign))) {
            $upperServices = $workOrder->workOrderServices->filter(fn($s) => ProductionStationHelper::getStationCode($s->category_name ?? $s->service?->name ?? '') === 'UPPER');
            $serviceIds = $upperServices->pluck('service_id')->filter()->toArray();

            $tech = $this->findBestTechnicianForStationAndServices('UPPER', $serviceIds);
            if ($tech) {
                $updates['prod_upper_by'] = $tech->id;
                foreach ($upperServices as $woService) {
                    if (!$woService->started_at) {
                        $woService->update(['technician_id' => $tech->id]);
                    }
                }
                Log::info("Auto-assigned Upper for SPK #{$workOrder->spk_number} to Technician: {$tech->name} (ID: {$tech->id})");
            }
        }

        // 3. Treatment Station
        if ($hasTreatment && (!$workOrder->prod_cleaning_by || $isDrShoeClean || (!$workOrder->prod_cleaning_started_at && $forceReassign))) {
            $treatmentServices = $workOrder->workOrderServices->filter(fn($s) => ProductionStationHelper::getStationCode($s->category_name ?? $s->service?->name ?? '') === 'TREATMENT');
            $serviceIds = $treatmentServices->pluck('service_id')->filter()->toArray();

            $tech = $this->findBestTechnicianForStationAndServices('TREATMENT', $serviceIds);
            if ($tech) {
                $updates['prod_cleaning_by'] = $tech->id;
                foreach ($workOrder->workOrderServices as $woService) {
                    if (!$woService->started_at) {
                        $woService->update(['technician_id' => $tech->id]);
                    }
                }
                Log::info("Auto-assigned Treatment for SPK #{$workOrder->spk_number} to Technician: {$tech->name} (ID: {$tech->id})");
            }
        }

        if (!empty($updates)) {
            $workOrder->update($updates);
        }
    }

    /**
     * Auto-assign technicians for all stations in QC stage
     */
    public function autoAssignQcTechnicians(WorkOrder $workOrder, bool $forceReassign = false): void
    {
        $updates = [];
        $workOrder->loadMissing(['workOrderServices.service', 'qcJahitBy', 'qcCleanupBy', 'qcFinalBy']);

        $hasJahitQc = $workOrder->workOrderServices->contains(fn($s) => 
            str_contains(strtolower($s->category_name ?? ''), 'sol') || 
            str_contains(strtolower($s->service?->name ?? ''), 'sol') ||
            str_contains(strtolower($s->category_name ?? ''), 'upper') || 
            str_contains(strtolower($s->service?->name ?? ''), 'upper') ||
            str_contains(strtolower($s->category_name ?? ''), 'jahit') || 
            str_contains(strtolower($s->service?->name ?? ''), 'jahit')
        );

        $isDrShoeJahit = str_contains($workOrder->qcJahitBy?->name ?? '', 'Dr. Shoe');
        $isDrShoeCleanup = str_contains($workOrder->qcCleanupBy?->name ?? '', 'Dr. Shoe');
        $isDrShoeFinal = str_contains($workOrder->qcFinalBy?->name ?? '', 'Dr. Shoe');

        if ($hasJahitQc && (!$workOrder->qc_jahit_by || $isDrShoeJahit || (!$workOrder->qc_jahit_started_at && $forceReassign))) {
            $tech = $this->findBestTechnicianForStationAndServices('QC', []);
            if ($tech) {
                $updates['qc_jahit_by'] = $tech->id;
            }
        }

        if (!$workOrder->qc_cleanup_by || $isDrShoeCleanup || (!$workOrder->qc_cleanup_started_at && $forceReassign)) {
            $tech = $this->findBestTechnicianForStationAndServices('QC', []);
            if ($tech) {
                $updates['qc_cleanup_by'] = $tech->id;
            }
        }

        if (!$workOrder->qc_final_by || $isDrShoeFinal || (!$workOrder->qc_final_started_at && $forceReassign)) {
            $tech = $this->findBestTechnicianForStationAndServices('QC', []);
            if ($tech) {
                $updates['qc_final_by'] = $tech->id;
            }
        }

        if (!empty($updates)) {
            $workOrder->update($updates);
        }
    }

    /**
     * Find best available technician using users.station AND technician_services skill matrix
     */
    public function findBestTechnicianForStationAndServices(string $stationCode, array $serviceIds = []): ?User
    {
        $query = User::where('is_active', true)
            ->where('role', 'technician')
            ->whereNotIn('specialization', $this->excludedSpecs)
            ->where('name', 'not like', '%Dr. Shoe%');

        // 1. Try finding technicians explicitly mapped to any of the serviceIds via technician_services
        if (!empty($serviceIds)) {
            $skilledCandidates = (clone $query)->whereHas('services', function ($sq) use ($serviceIds) {
                $sq->whereIn('services.id', $serviceIds);
            })->get();

            if ($skilledCandidates->isNotEmpty()) {
                return $this->pickLowestWorkloadTechnician($skilledCandidates);
            }
        }

        // 2. Fallback to technicians with matching station
        $stationCandidates = (clone $query)->where('station', $stationCode)->get();
        if ($stationCandidates->isNotEmpty()) {
            return $this->pickLowestWorkloadTechnician($stationCandidates);
        }

        // 3. Fallback to any active technician
        $allCandidates = $query->get();
        if ($allCandidates->isNotEmpty()) {
            return $this->pickLowestWorkloadTechnician($allCandidates);
        }

        return null;
    }

    /**
     * Pick technician with lowest active workload
     */
    protected function pickLowestWorkloadTechnician($candidates): ?User
    {
        return $candidates->sortBy(function ($tech) {
            return WorkOrderService::where('technician_id', $tech->id)
                ->where('status', '!=', 'COMPLETED')
                ->count();
        })->first();
    }

    /**
     * Get list of qualified technicians for a specific station
     */
    public function getQualifiedTechnicians(string $category)
    {
        $categoryUpper = strtoupper($category);
        $stationCode = 'TREATMENT';

        if (str_contains($categoryUpper, 'SOL')) {
            $stationCode = 'SOLING';
        } elseif (str_contains($categoryUpper, 'UPPER') || str_contains($categoryUpper, 'JAHIT')) {
            $stationCode = 'UPPER';
        } elseif (str_contains($categoryUpper, 'PREP') || str_contains($categoryUpper, 'WASH')) {
            $stationCode = 'PREPARATION';
        } elseif (str_contains($categoryUpper, 'QC')) {
            $stationCode = 'QC';
        }

        return User::where('is_active', true)
            ->where('role', 'technician')
            ->where(function ($q) use ($stationCode) {
                $q->where('station', $stationCode)
                  ->orWhereNull('station');
            })
            ->whereNotIn('specialization', $this->excludedSpecs)
            ->where('name', 'not like', '%Dr. Shoe%')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get candidate technicians for Prep Washing (Preparation Station)
     */
    public static function getPrepWashingCandidates()
    {
        return User::where('is_active', true)
            ->where('role', 'technician')
            ->where(function ($q) {
                $q->where('station', 'PREPARATION')
                  ->orWhere('specialization', 'like', '%Cuci%')
                  ->orWhere('specialization', 'like', '%Washing%');
            })
            ->whereNotIn('specialization', ['PIC Material Sol', 'PIC Material Upper', 'PIC Material', 'PIC MATERIAL SOL', 'PIC MATERIAL UPPER'])
            ->where('name', 'not like', '%Dr. Shoe%')
            ->get();
    }

    /**
     * Get recommended prep washing technician for a work order
     */
    public function getRecommendedPrepWashingTechnician(WorkOrder $order, $candidates = null): ?User
    {
        if (!$candidates || $candidates->isEmpty()) {
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
