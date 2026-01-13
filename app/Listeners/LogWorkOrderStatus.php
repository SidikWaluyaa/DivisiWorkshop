<?php

namespace App\Listeners;

use App\Events\WorkOrderStatusUpdated;
use App\Models\WorkOrderLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogWorkOrderStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WorkOrderStatusUpdated $event): void
    {
        $order = $event->workOrder;
        
        // Log entry
        $log = new WorkOrderLog();
        $log->work_order_id = $order->id;
        $log->step = $event->newStatus->value; // New Status
        $log->action = 'STATUS_CHANGE';
        $log->user_id = $event->userId;
        $log->description = "Status berubah dari {$event->oldStatus->value} ke {$event->newStatus->value}. Note: {$event->note}";
        $log->created_at = now();
        $log->save();
    }
}
