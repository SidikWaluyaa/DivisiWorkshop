<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display the comprehensive detail of a work order.
     */
    public function show($id)
    {
        $order = WorkOrder::with([
            'customer',
            'services',
            'materials',
            'photos.uploader', 
            'logs.user',
            'payments'
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function printShippingLabel($id)
    {
        $order = WorkOrder::with(['customer'])->findOrFail($id);
        return view('admin.orders.shipping-label', compact('order'));
    }
}
