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
     * Excluded non-repair staff (PIC Material & System Placeholders)
     */
    protected array $excludedSpecs = ['PIC Material Sol', 'PIC Material Upper', 'PIC Material', 'PIC MATERIAL SOL', 'PIC MATERIAL UPPER'];

    /**
     * 1. STATION PREPARATION: Auto-assign prep (washing/bongkar) technician evenly based on workload
     * Sub-tasks: Washing (Cuci), Bongkar Sol, Bongkar Upper
     */
    public function autoAssignPrepWashing(WorkOrder $workOrder, bool $forceReassign = false): ?User
    {
        if ($workOrder->prep_washing_by && !$forceReassign && !$workOrder->prep_washing_started_at) {
            $existing = User::find($workOrder->prep_washing_by);
            if ($existing && !str_contains($existing->name, 'Dr. Shoe')) {
                return $existing;
            }
        }

        // Candidates: Active technicians in Station Preparation (Washing, Bongkar Sol, Bongkar Upper)
        $candidates = User::where('is_active', true)
            ->whereIn('role', ['technician', 'technician_assistant'])
            ->where(function ($q) {
                $q->where('station', 'PREPARATION')
                  ->orWhereIn('specialization', ['Washing', 'Cuci', 'Bongkar Sol', 'Bongkar Upper']);
            })
            ->whereNotIn('specialization', $this->excludedSpecs)
            ->where('name', 'not like', '%Dr. Shoe%')
            ->get();

        if ($candidates->isEmpty()) {
            $candidates = User::whereIn('role', ['technician', 'technician_assistant'])
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
     * 2. STATION PRODUCTION: Auto-assign technicians for all services in Production stage
     * Sub-tasks: Reparasi Sol (Soling), Reparasi Upper (Upper), Reparasi Treatment (Treatment)
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

        // a. Reparasi Sol (Station: SOLING, Spec: Reparasi Sol)
        if ($hasSol && (!$workOrder->prod_sol_by || $isDrShoeSol || (!$workOrder->prod_sol_started_at && $forceReassign))) {
            $solServices = $workOrder->workOrderServices->filter(fn($s) => ProductionStationHelper::getStationCode($s->category_name ?? $s->service?->name ?? '') === 'SOLING');
            $serviceIds = $solServices->pluck('service_id')->filter()->toArray();

            $tech = $this->findBestTechnicianForStationAndServices('SOLING', $serviceIds, ['Reparasi Sol', 'Sol Repair']);
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

        // b. Reparasi Upper (Station: UPPER, Spec: Reparasi Upper)
        if ($hasUpper && (!$workOrder->prod_upper_by || $isDrShoeUpper || (!$workOrder->prod_upper_started_at && $forceReassign))) {
            $upperServices = $workOrder->workOrderServices->filter(fn($s) => ProductionStationHelper::getStationCode($s->category_name ?? $s->service?->name ?? '') === 'UPPER');
            $serviceIds = $upperServices->pluck('service_id')->filter()->toArray();

            $tech = $this->findBestTechnicianForStationAndServices('UPPER', $serviceIds, ['Reparasi Upper', 'Upper Repair']);
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

        // c. Reparasi Treatment (Station: TREATMENT, Spec: Reparasi Treatment)
        if ($hasTreatment && (!$workOrder->prod_cleaning_by || $isDrShoeClean || (!$workOrder->prod_cleaning_started_at && $forceReassign))) {
            $treatmentServices = $workOrder->workOrderServices->filter(fn($s) => ProductionStationHelper::getStationCode($s->category_name ?? $s->service?->name ?? '') === 'TREATMENT');
            $serviceIds = $treatmentServices->pluck('service_id')->filter()->toArray();

            $tech = $this->findBestTechnicianForStationAndServices('TREATMENT', $serviceIds, ['Reparasi Treatment', 'Treatment', 'Repaint']);
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
     * 3. STATION QC: Auto-assign technicians for all stations in QC stage
     * Sub-tasks: QC Jahit, QC Cleanup, QC Final
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

        // a. QC Jahit (Spec: QC Jahit)
        if ($hasJahitQc && (!$workOrder->qc_jahit_by || $isDrShoeJahit || (!$workOrder->qc_jahit_started_at && $forceReassign))) {
            $tech = $this->findBestTechnicianForStationAndServices('QC', [], ['QC Jahit', 'Jahit']);
            if ($tech) {
                $updates['qc_jahit_by'] = $tech->id;
            }
        }

        // b. QC Cleanup (Spec: QC Cleanup)
        if (!$workOrder->qc_cleanup_by || $isDrShoeCleanup || (!$workOrder->qc_cleanup_started_at && $forceReassign)) {
            $tech = $this->findBestTechnicianForStationAndServices('QC', [], ['QC Cleanup', 'Clean Up']);
            if ($tech) {
                $updates['qc_cleanup_by'] = $tech->id;
            }
        }

        // c. QC Final (Spec: QC Final)
        if (!$workOrder->qc_final_by || $isDrShoeFinal || (!$workOrder->qc_final_started_at && $forceReassign)) {
            $tech = $this->findBestTechnicianForStationAndServices('QC', [], ['QC Final', 'PIC QC']);
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
    public function findBestTechnicianForStationAndServices(string $stationCode, array $serviceIds = [], array $specializations = []): ?User
    {
        $query = User::where('is_active', true)
            ->whereIn('role', ['technician', 'technician_assistant', 'qc'])
            ->whereNotIn('specialization', $this->excludedSpecs)
            ->where('name', 'not like', '%Dr. Shoe%');

        // 1. Try finding technicians by specialization if provided
        if (!empty($specializations)) {
            $specCandidates = (clone $query)->whereIn('specialization', $specializations)->get();
            if ($specCandidates->isNotEmpty()) {
                return $this->pickLowestWorkloadTechnician($specCandidates);
            }
        }

        // 2. Try finding technicians explicitly mapped to any of the serviceIds via technician_services
        if (!empty($serviceIds)) {
            $skilledCandidates = (clone $query)->whereHas('services', function ($sq) use ($serviceIds) {
                $sq->whereIn('services.id', $serviceIds);
            })->get();

            if ($skilledCandidates->isNotEmpty()) {
                return $this->pickLowestWorkloadTechnician($skilledCandidates);
            }
        }

        // 3. Fallback to technicians with matching station
        $stationCandidates = (clone $query)->where('station', $stationCode)->get();
        if ($stationCandidates->isNotEmpty()) {
            return $this->pickLowestWorkloadTechnician($stationCandidates);
        }

        // 4. Fallback to any active technician
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
     * Get candidates for prep washing stage
     */
    public static function getPrepWashingCandidates()
    {
        $excludedSpecs = ['PIC Material Sol', 'PIC Material Upper', 'PIC Material', 'PIC MATERIAL SOL', 'PIC MATERIAL UPPER'];
        
        $candidates = User::where('is_active', true)
            ->whereIn('role', ['technician', 'technician_assistant'])
            ->where(function ($q) {
                $q->where('station', 'PREPARATION')
                  ->orWhereIn('specialization', ['Washing', 'Cuci', 'Bongkar Sol', 'Bongkar Upper']);
            })
            ->whereNotIn('specialization', $excludedSpecs)
            ->where('name', 'not like', '%Dr. Shoe%')
            ->get();

        if ($candidates->isEmpty()) {
            $candidates = User::whereIn('role', ['technician', 'technician_assistant'])
                ->where('is_active', true)
                ->whereNotIn('specialization', $excludedSpecs)
                ->where('name', 'not like', '%Dr. Shoe%')
                ->get();
        }

        return $candidates;
    }

    /**
     * Get qualified technicians for specific sub-station
     */
    public function getQualifiedTechnicians(string $type)
    {
        $specializations = match(strtolower($type)) {
            'sol', 'bongkar_sol', 'soling' => ['Bongkar Sol', 'Reparasi Sol', 'Soling', 'Sol'],
            'upper', 'bongkar_upper' => ['Bongkar Upper', 'Reparasi Upper', 'Upper'],
            'washing', 'cuci' => ['Washing', 'Cuci'],
            default => []
        };

        $query = User::where('is_active', true)
            ->whereIn('role', ['technician', 'technician_assistant'])
            ->whereNotIn('specialization', $this->excludedSpecs)
            ->where('name', 'not like', '%Dr. Shoe%');

        if (!empty($specializations)) {
            $specQuery = (clone $query)->whereIn('specialization', $specializations)->get();
            if ($specQuery->isNotEmpty()) {
                return $specQuery;
            }
        }

        return $query->get();
    }

    /**
     * Get recommended prep washing technician for an order
     */
    public function getRecommendedPrepWashingTechnician(WorkOrder $order, $candidates = null): ?User
    {
        $candidates = $candidates ?? self::getPrepWashingCandidates();
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
