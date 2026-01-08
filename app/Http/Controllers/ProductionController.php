<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;

class ProductionController extends Controller
{
    protected WorkflowService $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index()
    {
        // 1. Queue: Ready for Production (From Sortir)
        $queue = WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
                    ->whereNull('taken_date') // Not yet taken by technician
                    ->with('services')
                    ->orderBy('updated_at', 'asc')
                    ->get();

        // Check for revision status (has been to QC before)
        $queue->transform(function ($order) {
            $order->is_revisi = $order->logs()->where('step', WorkOrderStatus::QC->value)->exists();
            return $order;
        });

        // 2. In Progress: Being worked on
        $inProgress = WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
                    ->whereNotNull('taken_date')
                    ->with('services')
                    ->orderBy('updated_at', 'desc')
                    ->get();

        $inProgress->transform(function ($order) {
            $order->is_revisi = $order->logs()->where('step', WorkOrderStatus::QC->value)->exists();
            return $order;
        });
                    
        $technicians = \App\Models\User::all();
                    
        return view('production.index', compact('queue', 'inProgress', 'technicians'));
    }

    public function start(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        
        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);
        
        $technicianId = $request->technician_id;
        
        // Save technician to production field
        $order->technician_production_id = $technicianId;
        $order->taken_date = now();
        $order->save();
        
        $order->logs()->create([
            'step' => WorkOrderStatus::PRODUCTION->value,
            'action' => 'STARTED',
            'user_id' => $technicianId,
            'description' => 'Technician started production.'
        ]);
        
        return back()->with('success', 'Sepatu diambil. Selamat bekerja!');
    }

    public function finish(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        
        // Technician already set when started, no need to validate again
        // Move to QC
        $this->workflow->updateStatus($order, WorkOrderStatus::QC, 'Production Completed.');
        
        return redirect()->back()->with('success', 'Produksi selesai. Kirim ke QC.');
    }
}
