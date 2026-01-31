<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkOrder;
use App\Http\Controllers\FinanceController;
use App\Services\WorkflowService;

class RecalculateFinanceFields extends Command
{
    protected $signature = 'finance:recalculate {order_id?}';
    protected $description = 'Recalculate finance fields for work orders';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        
        if ($orderId) {
            $orders = WorkOrder::where('id', $orderId)->get();
        } else {
            $orders = WorkOrder::whereNotNull('total_transaksi')->get();
        }
        
        $controller = new FinanceController(new WorkflowService());
        $method = new \ReflectionMethod($controller, 'calculateFinanceFields');
        $method->setAccessible(true);
        
        foreach ($orders as $order) {
            // Ensure we're working with a WorkOrder model
            if (!$order instanceof WorkOrder) {
                $this->error("Skipping invalid order object");
                continue;
            }
            
            // Call calculateFinanceFields and capture the returned order
            $updatedOrder = $method->invoke($controller, $order);
            
            // Use the returned order if available, otherwise use the original
            $orderToSave = $updatedOrder instanceof WorkOrder ? $updatedOrder : $order;
            
            $orderToSave->save();
            $this->info("Updated Order #{$orderToSave->id} - {$orderToSave->spk_number}: Total Paid = {$orderToSave->total_paid}, Sisa = {$orderToSave->sisa_tagihan}");
        }
        
        $this->info("Recalculated {$orders->count()} orders.");
    }
}
