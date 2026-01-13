<?php

namespace App\Events;

use App\Enums\WorkOrderStatus;
use App\Models\WorkOrder;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkOrderStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $workOrder;
    public $oldStatus;
    public $newStatus;
    public $note;
    public $userId;

    /**
     * Create a new event instance.
     *
     * @param WorkOrder $workOrder
     * @param WorkOrderStatus $oldStatus
     * @param WorkOrderStatus $newStatus
     * @param string|null $note
     * @param int|null $userId
     */
    public function __construct(WorkOrder $workOrder, WorkOrderStatus $oldStatus, WorkOrderStatus $newStatus, ?string $note, ?int $userId)
    {
        $this->workOrder = $workOrder;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->note = $note;
        $this->userId = $userId;
    }
}
