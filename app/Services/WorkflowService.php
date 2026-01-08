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

        DB::transaction(function () use ($workOrder, $newStatus, $note, $userId) {
            $oldStatus = $workOrder->status;
            
            // 2. Update Order
            $workOrder->status = $newStatus->value;
            
            // Update location based on status map
            $workOrder->current_location = $this->getDefaultLocationForStatus($newStatus);
            
            $workOrder->save();

            // 3. Log
            WorkOrderLog::create([
                'work_order_id' => $workOrder->id,
                'step' => $newStatus->value,
                'action' => 'MOVED',
                'user_id' => $userId ?? Auth::id(),
                'description' => "Status changed from $oldStatus to " . $newStatus->value . ". " . ($note ?? ''),
            ]);
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
        // Placeholder for strict rules
        // if ($newStatus === WorkOrderStatus::PRODUCTION && !$workOrder->materialsReady()) {
        //     throw new Exception("Material belum ready!");
        // }
    }
}
