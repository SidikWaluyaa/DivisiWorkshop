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
        $oldStatusObj = WorkOrderStatus::tryFrom($workOrder->status) ?? WorkOrderStatus::DITERIMA;

        DB::transaction(function () use ($workOrder, $newStatus, $note, $userId, $oldStatusObj) {
            // 2. Update Order
            $workOrder->status = $newStatus->value;
            
            // Update location based on status map
            $workOrder->current_location = $this->getDefaultLocationForStatus($newStatus);

            // Set finished_date if moving to SELESAI
            if ($newStatus === WorkOrderStatus::SELESAI && is_null($workOrder->finished_date)) {
                $workOrder->finished_date = now();
            }
            
            $workOrder->save();

            // 3. Dispatch Event
            \App\Events\WorkOrderStatusUpdated::dispatch(
                $workOrder, 
                $oldStatusObj, 
                $newStatus, 
                $note, 
                $userId ?? Auth::id()
            );
        });
    }

    private function getDefaultLocationForStatus(WorkOrderStatus $status): string
    {
        return match($status) {
            WorkOrderStatus::DITERIMA => 'Gudang Penerimaan',
            WorkOrderStatus::ASSESSMENT => 'Rak Sepatu',
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
        $currentStatus = WorkOrderStatus::tryFrom($workOrder->status);
        
        // Allowed transitions map
        // Key: Current Status -> Values: Allowed Next Statuses
        $allowed = [
            WorkOrderStatus::DITERIMA->value => [
                WorkOrderStatus::SORTIR, 
                WorkOrderStatus::ASSESSMENT, // Optional path
                WorkOrderStatus::BATAL
            ],
            WorkOrderStatus::ASSESSMENT->value => [
                WorkOrderStatus::PREPARATION, // Valid flow: Assessment -> Preparation
                WorkOrderStatus::SORTIR, 
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
                WorkOrderStatus::PREPARATION // Upsell (Tambah Layanan) -> Back to Prep
            ],
            WorkOrderStatus::DIANTAR->value => [
                WorkOrderStatus::SELESAI // Delivery failed/returned
            ],
             WorkOrderStatus::BATAL->value => [
                 // Once cancelled, maybe reopen?
                 WorkOrderStatus::DITERIMA 
             ]
        ];

        // If current status is unknown or mapped, check rules
        if ($currentStatus && isset($allowed[$currentStatus->value])) {
            $canMove = false;
            foreach ($allowed[$currentStatus->value] as $target) {
                if ($newStatus === $target) {
                    $canMove = true;
                    break;
                }
            }

            if (!$canMove) {
                throw new Exception("Perubahan status tidak valid: dari {$currentStatus->value} ke {$newStatus->value} tidak diperbolehkan.");
            }
        }
        
        // Execute strict business rules
        $this->checkBusinessRules($workOrder, $newStatus);
    }

    protected function checkBusinessRules(WorkOrder $workOrder, WorkOrderStatus $newStatus)
    {
        // Rule 0: PREPARATION -> SORTIR (Must be "Ready")
        if ($newStatus === WorkOrderStatus::SORTIR) {
            if ($workOrder->status === WorkOrderStatus::PREPARATION->value) {
                if (!$workOrder->is_ready) {
                     // Get detail of what is missing? Accessor just returns bool.
                     throw new Exception("Tahapan Preparation (Cuci/Bongkar) belum selesai sepenuhnya.");
                }
            }
        }

        // Rule: SORTIR -> PRODUCTION (Materials must be Ready)
        if ($newStatus === WorkOrderStatus::PRODUCTION) {
             if ($workOrder->status === WorkOrderStatus::SORTIR->value) {
                 if (!$workOrder->is_sortir_finished) {
                     throw new Exception("Masih ada material yang berstatus REQUESTED. Mohon selesaikan pengadaan material.");
                 }
             }
        }

        // Rule 1: Cannot move to QC if Production tasks are not finished
        if ($newStatus === WorkOrderStatus::QC) {
            // Check if coming from Production
            if ($workOrder->status === WorkOrderStatus::PRODUCTION->value) {
                if (!$workOrder->is_production_finished) {
                     throw new Exception("Semua proses produksi (Sol/Upper/Cleaning) harus diselesaikan sebelum masuk QC.");
                }
            }
        }

        // Rule 2: Cannot move to SELESAI if QC checks are not finished
        if ($newStatus === WorkOrderStatus::SELESAI) {
             if ($workOrder->status === WorkOrderStatus::QC->value) {
                 if (!$workOrder->is_qc_finished) {
                     throw new Exception("Semua proses QC (Jahit/Cleanup/Final) harus diselesaikan sebelum finish.");
                 }
             }
        }
    }
}
