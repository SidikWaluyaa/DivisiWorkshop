<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;

class VisualReportController extends Controller
{
    public function show($token)
    {
        $order = WorkOrder::where('invoice_token', $token)
            ->with(['photos', 'workOrderServices.service'])
            ->firstOrFail();

        // Categorize photos
        $beforeSteps = ['RECEPTION', 'WAREHOUSE_BEFORE', 'ASSESSMENT', 'SORTIR'];
        
        $beforePhotos = $order->photos->whereIn('step', $beforeSteps)->sortBy('created_at');
        
        // If no strict before steps, at least show everything that isn't FINISH or SELESAI
        if ($beforePhotos->isEmpty()) {
            $beforePhotos = $order->photos->whereNotIn('step', ['FINISH', 'SELESAI'])->sortBy('created_at');
        }

        $afterPhotos = $order->photos->whereIn('step', ['FINISH', 'SELESAI'])->sortBy('created_at');

        return view('tracking.visual-report', compact('order', 'beforePhotos', 'afterPhotos'));
    }
}
