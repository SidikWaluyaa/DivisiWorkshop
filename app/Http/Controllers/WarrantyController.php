<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function printSpk($id)
    {
        $warranty = \App\Models\WorkOrderWarranty::with(['workOrder.workOrderServices', 'workOrder.photos'])->findOrFail($id);
        $order = $warranty->workOrder;

        return view('garansi.print-spk', compact('warranty', 'order'));
    }
}
