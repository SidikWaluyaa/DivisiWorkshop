<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class WorkflowService
{
    /**
     * Move the order to the specified status, with validations.
     */
    public function updateStatus(WorkOrder $workOrder, WorkOrderStatus $newStatus, ?string $note = null, ?int $userId = null): void
    {
        // 1. Validate Transition
        $this->validateTransition($workOrder, $newStatus);

        // Capture old status object before change (assuming current status is valid enum)
        $oldStatusObj = $workOrder->status instanceof WorkOrderStatus 
            ? $workOrder->status 
            : (WorkOrderStatus::tryFrom($workOrder->status) ?? WorkOrderStatus::DITERIMA);

        DB::transaction(function () use ($workOrder, $newStatus, $note, $userId, $oldStatusObj) {
            // 2. Update Order
            $workOrder->status = $newStatus;
            
            // Update location based on status map
            $workOrder->current_location = $this->getDefaultLocationForStatus($newStatus);

            // Set finished_date if moving to SELESAI
            if ($newStatus === WorkOrderStatus::SELESAI && is_null($workOrder->finished_date)) {
                $workOrder->finished_date = now();
            }
            
            $workOrder->save();
            
            // 3. Dispatch Event
            $this->dispatchUpdateEvent($workOrder, $oldStatusObj, $newStatus, $note, $userId);

            // Auto-release from Inbound Rack when status becomes PREPARATION
            if ($newStatus === WorkOrderStatus::PREPARATION) {
                app(\App\Services\Storage\StorageService::class)->releaseFromInbound($workOrder);
            }

            // Auto-release from Finish Warehouse when status becomes DIANTAR (Sent)
            if ($newStatus === WorkOrderStatus::DIANTAR && $workOrder->storage_rack_code) {
                try {
                    app(\App\Services\Storage\StorageService::class)->retrieveFromStorage($workOrder->id, 'Sent to Customer (Delivery)');
                } catch (Exception $e) {
                    // Log or handle if needed, but don't block workflow
                    \Illuminate\Support\Facades\Log::warning("WorkflowService: Failed to auto-release from storage for DIANTAR: " . $e->getMessage());
                }
            }
        });
    }

    /**
     * Move the order back to a previous stage for revision.
     */
    public function revise(WorkOrder $workOrder, WorkOrderStatus $targetStatus, string $reason, array $stationsToReset = []): void
    {
        DB::transaction(function () use ($workOrder, $targetStatus, $reason, $stationsToReset) {
            $oldStatus = $workOrder->status;
            
            // 1. Update Status
            $workOrder->previous_status = $workOrder->status;
            $workOrder->status = $targetStatus;
            $workOrder->is_revising = true;
            $workOrder->current_location = $this->getDefaultLocationForStatus($targetStatus);

            // 2. Reset specific stations if requested
            // stationsToReset format: ['prep_washing', 'prod_sol', etc]
            foreach ($stationsToReset as $station) {
                $workOrder->{"{$station}_completed_at"} = null;
                // Optional: We can also clear the technician if we want full restart
                // $workOrder->{"{$station}_by"} = null;
            }

            $workOrder->save();

            // 3. Log the Revision Detail
            WorkOrderLog::create([
                'work_order_id' => $workOrder->id,
                'user_id' => Auth::id(),
                'action' => 'REVISION_REQUESTED',
                'description' => "REVISI dari " . ($oldStatus instanceof WorkOrderStatus ? $oldStatus->value : $oldStatus) . 
                                 " ke " . $targetStatus->value . ". Alasan: " . $reason,
                'step' => $targetStatus->value
            ]);
        });
    }

    /**
     * Dispatch Event
     */
    private function dispatchUpdateEvent(WorkOrder $workOrder, WorkOrderStatus $oldStatus, WorkOrderStatus $newStatus, ?string $note, ?int $userId): void
    {
         \App\Events\WorkOrderStatusUpdated::dispatch(
            $workOrder, 
            $oldStatus, 
            $newStatus, 
            $note, 
            $userId ?? Auth::id()
        );
    }

    private function getDefaultLocationForStatus(WorkOrderStatus $status): string
    {
        return match($status) {
            WorkOrderStatus::DITERIMA => 'Gudang Penerimaan',
            WorkOrderStatus::ASSESSMENT => 'Rak Sepatu',
            WorkOrderStatus::READY_TO_DISPATCH => 'Gudang (Siap Kirim)',
            WorkOrderStatus::OTW_WORKSHOP => 'Pengiriman (OTW)',
            WorkOrderStatus::PREPARATION => 'Rumah Hijau',
            WorkOrderStatus::SORTIR => 'Rumah Hijau',
            WorkOrderStatus::PRODUCTION => 'Rumah Abu',
            WorkOrderStatus::QC => 'Rumah Abu',
            WorkOrderStatus::SELESAI => 'Rak Selesai / Pickup Area (Rumah Hijau)',
            default => 'Unknown',
        };
    }

    protected function validateTransition(WorkOrder $workOrder, WorkOrderStatus $newStatus)
    {
        $currentStatus = $workOrder->status instanceof WorkOrderStatus 
            ? $workOrder->status 
            : WorkOrderStatus::tryFrom($workOrder->status);
        
        // Allow same-status update (Idempotent)
        if ($currentStatus && $currentStatus === $newStatus) {
            return;
        }

        // Allowed transitions map
        // Key: Current Status -> Values: Allowed Next Statuses
        $allowed = [
            WorkOrderStatus::DITERIMA->value => [
                WorkOrderStatus::ASSESSMENT, // Main Flow: QC Lolos -> Assessment
                WorkOrderStatus::SORTIR, // Legacy/Direct
                WorkOrderStatus::PREPARATION, // Direct Bypass
                WorkOrderStatus::BATAL
            ],
            WorkOrderStatus::ASSESSMENT->value => [
                WorkOrderStatus::WAITING_PAYMENT, // Main Flow: Assessment -> Finance
                WorkOrderStatus::PREPARATION, // Legacy/Shortcut
                WorkOrderStatus::SORTIR, 
                WorkOrderStatus::BATAL
            ],
            WorkOrderStatus::WAITING_PAYMENT->value => [
                WorkOrderStatus::READY_TO_DISPATCH, // Payment confirmed -> Ready to send
                WorkOrderStatus::PREPARATION, // Allow direct if needed (legacy/special)
                WorkOrderStatus::BATAL
            ],
            WorkOrderStatus::READY_TO_DISPATCH->value => [
                WorkOrderStatus::OTW_WORKSHOP,
                WorkOrderStatus::PREPARATION, // Allow bypass if already physically there
                WorkOrderStatus::BATAL
            ],
            WorkOrderStatus::OTW_WORKSHOP->value => [
                WorkOrderStatus::PREPARATION,
                WorkOrderStatus::READY_TO_DISPATCH, // Rollback manifest
                WorkOrderStatus::BATAL
            ],
            WorkOrderStatus::SORTIR->value => [
                WorkOrderStatus::PREPARATION,
                WorkOrderStatus::PRODUCTION, // Shortcut if no prep needed
                WorkOrderStatus::BATAL
            ],
            WorkOrderStatus::PREPARATION->value => [
                WorkOrderStatus::PRODUCTION,
                WorkOrderStatus::SORTIR, // Back for re-check
                WorkOrderStatus::BATAL
            ],
            WorkOrderStatus::PRODUCTION->value => [
                WorkOrderStatus::QC,
                WorkOrderStatus::PREPARATION, // Backtrack
                WorkOrderStatus::BATAL
            ],
            WorkOrderStatus::QC->value => [
                WorkOrderStatus::SELESAI,
                WorkOrderStatus::PRODUCTION, // Revisi (Return to Prod)
                WorkOrderStatus::PREPARATION, // Revisi (Return to Prep)
                WorkOrderStatus::BATAL
            ],
            WorkOrderStatus::SELESAI->value => [
                WorkOrderStatus::DIANTAR, // Delivery
                WorkOrderStatus::QC, // Re-open if customer complains
                WorkOrderStatus::PREPARATION, // Upsell (Tambah Layanan) -> Back to Prep
                WorkOrderStatus::CX_FOLLOWUP // If complaint/issue reported post-finish
            ],
            WorkOrderStatus::DIANTAR->value => [
                WorkOrderStatus::SELESAI // Delivery failed/returned
            ],
             WorkOrderStatus::BATAL->value => [
                 // Once cancelled, maybe reopen?
                 WorkOrderStatus::DITERIMA 
             ],
             // Add CX_FOLLOWUP transitions
             WorkOrderStatus::CX_FOLLOWUP->value => [
                WorkOrderStatus::ASSESSMENT,
                WorkOrderStatus::WAITING_PAYMENT,
                WorkOrderStatus::SORTIR,      // Back for material validation
                WorkOrderStatus::PREPARATION, // Resume to Prep
                WorkOrderStatus::PRODUCTION,  // Resume to Prod
                WorkOrderStatus::QC,          // Resume to QC
                WorkOrderStatus::SELESAI,     // Resume to Finish
                WorkOrderStatus::DITERIMA,    // Resume to Reception
                WorkOrderStatus::BATAL
             ],
             // Also add CX_FOLLOWUP as a target for others (Global catch-all usually, but explicit here)
             'GLOBAL' => [WorkOrderStatus::CX_FOLLOWUP] // Logic tweak needed below if using GLOBAL
        ];

        // If current status is unknown or mapped, check rules
        if ($currentStatus && isset($allowed[$currentStatus->value])) {
            $canMove = false;
            
            // Allow CX_FOLLOWUP from ANY status
            if ($newStatus === WorkOrderStatus::CX_FOLLOWUP) {
                $canMove = true;
            } 
            // Boomerang: Allow return to previous status if revising
            elseif ($workOrder->is_revising && $workOrder->previous_status) {
                $prevValue = $workOrder->previous_status instanceof WorkOrderStatus ? $workOrder->previous_status->value : $workOrder->previous_status;
                $newValue = $newStatus instanceof WorkOrderStatus ? $newStatus->value : $newStatus;
                
                if ($prevValue === $newValue) {
                    $canMove = true;
                }
            }
            
            if (!$canMove) {
                // Log the blocked attempt for debugging
                \Illuminate\Support\Facades\Log::warning("Blocked workflow transition for Order #{$workOrder->spk_number}: " . 
                    ($currentStatus ? $currentStatus->value : 'NULL') . " -> " . $newStatus->value . 
                    " (Is Revising: " . ($workOrder->is_revising ? 'YES' : 'NO') . 
                    ", Prev Status: " . ($workOrder->previous_status instanceof WorkOrderStatus ? $workOrder->previous_status->value : ($workOrder->previous_status ?? 'NONE')) . ")");

                foreach ($allowed[$currentStatus->value] as $target) {
                    if ($newStatus === $target) {
                        $canMove = true;
                        break;
                    }
                }
            }

            if (!$canMove) {
                throw new Exception("Perubahan status tidak valid: dari {$currentStatus->value} ke {$newStatus->value} tidak diperbolehkan.");
            }
        }
        // If current status is not in allowed map (e.g. unknown), we might block or allow.
        // For safety, if we implemented strict map, we should block.
        // But for now, let's assume if key doesn't exist, we permit (or restrict?). 
        // Given the code structure, it only checks if key exists. So unmapped keys allow everything? 
        // Let's keep it as is, just ensuring CX_FOLLOWUP is global.
        
        // Execute strict business rules
        $this->checkBusinessRules($workOrder, $newStatus);
    }

    protected function checkBusinessRules(WorkOrder $workOrder, WorkOrderStatus $newStatus)
    {
        // Rule 0: PREPARATION -> SORTIR (Must be "Ready")
        if ($newStatus === WorkOrderStatus::SORTIR) {
            if ($workOrder->status === WorkOrderStatus::PREPARATION) {
                if (!$workOrder->is_ready) {
                     // Get detail of what is missing? Accessor just returns bool.
                     throw new Exception("Tahapan Preparation (Cuci/Bongkar) belum selesai sepenuhnya.");
                }
            }
        }

        // Rule: SORTIR -> PRODUCTION (Materials must be Ready)
        if ($newStatus === WorkOrderStatus::PRODUCTION) {
             if ($workOrder->status === WorkOrderStatus::SORTIR) {
                 if (!$workOrder->is_sortir_finished) {
                     throw new Exception("Masih ada material yang berstatus REQUESTED. Mohon selesaikan pengadaan material.");
                 }
             }
        }

        // Rule 1: Cannot move to QC if Production tasks are not finished
        if ($newStatus === WorkOrderStatus::QC) {
            // Check if coming from Production
            if ($workOrder->status === WorkOrderStatus::PRODUCTION) {
                if (!$workOrder->is_production_finished) {
                     throw new Exception("Semua proses produksi (Sol/Upper/Cleaning) harus diselesaikan sebelum masuk QC.");
                }
            }
        }

        // Rule 2: Cannot move to SELESAI if QC checks are not finished
        if ($newStatus === WorkOrderStatus::SELESAI) {
             if ($workOrder->status === WorkOrderStatus::QC) {
                 if (!$workOrder->is_qc_finished) {
                     throw new Exception("Semua proses QC (Jahit/Cleanup/Final) harus diselesaikan sebelum finish.");
                 }
             }
        }
    }
}
