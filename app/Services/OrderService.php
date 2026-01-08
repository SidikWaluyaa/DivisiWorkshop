<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Str;

class OrderService
{
    protected WorkflowService $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function createOrder(array $data)
    {
        // 1. Generate SPK Number if not present
        if (empty($data['spk_number'])) {
            $data['spk_number'] = 'SPK-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        }

        // 2. Set Defaults
        $data['status'] = WorkOrderStatus::DITERIMA->value;
        $data['entry_date'] = $data['entry_date'] ?? now();
        $data['location'] = 'Rak Penerimaan';

        // 3. Create Record
        $order = WorkOrder::create($data);

        // 4. Initial Log
        // We can use WorkflowService to "move" it to DITERIMA to trigger logs, 
        // but creation is slightly different. Let's just log manually or call generic logger.
        // Actually, let's just leave it created.
        
        return $order;
    }
}
