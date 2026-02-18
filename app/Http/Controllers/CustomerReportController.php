<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Http\Request;

class CustomerReportController extends Controller
{
    /**
     * Display the digital report portal for customers.
     * Uses a secure unique token instead of ID.
     */
    public function show($token)
    {
        // 1. Find work order by secure token
        $order = WorkOrder::where('invoice_token', $token)->firstOrFail();

        // 2. Security: Only allow for finished orders
        // Note: We can allow DIANTAR too as it's a "post-finish" state
        $allowedStatuses = [WorkOrderStatus::SELESAI, WorkOrderStatus::DIANTAR];
        if (!in_array($order->status, $allowedStatuses)) {
            return view('customer.report-pending', compact('order'));
        }

        // 3. Fetch FINISH photos (strictly as requested)
        $photos = $order->photos()
            ->where('step', 'FINISH')
            ->orderBy('created_at', 'asc')
            ->get();

        // 4. Return premium view
        return view('customer.customer-report', [
            'workOrder' => $order,
            'photos' => $photos,
            'generatedAt' => now()->format('d M Y H:i')
        ]);
    }
}
