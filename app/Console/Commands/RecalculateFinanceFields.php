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
            $method->invoke($controller, $order);
            $order->save();
            $this->info("Updated Order #{$order->id} - {$order->spk_number}: Total Paid = {$order->total_paid}, Sisa = {$order->sisa_tagihan}");
        }
        
        $this->info("Recalculated {$orders->count()} orders.");
    }
}
