<?php

namespace App\Listeners;

use App\Events\WorkOrderStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWhatsappNotification implements ShouldQueue
{
    use InteractsWithQueue;

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
        $customerName = $order->customer_name;
        $status = $event->newStatus->label();

        // Simulate sending WA
        // In real world: ApiService::send($order->customer_phone, "Your order is now $status");
        
        Log::info(" [WA MOCK] Sending WhatsApp to {$customerName} ({$order->customer_phone}): Status changed to {$status}");
    }
}
