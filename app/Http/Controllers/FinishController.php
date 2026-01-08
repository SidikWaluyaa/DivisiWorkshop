<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;

class FinishController extends Controller
{
    protected WorkflowService $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index()
    {
        // 1. Ready for Pickup (SELESAI)
        $ready = WorkOrder::where('status', WorkOrderStatus::SELESAI->value)
                    ->orderBy('finished_date', 'desc')
                    ->get();

        // 2. Taken/Completed History (Last 50 maybe?)
        // If status is still SELESAI but taken_date is filled? 
        // Or we might not change status to 'DIAMBIL' in enum to keep it simple, just check taken_date.
        // Let's assume 'SELESAI' is the status, 'taken_date' marks it gone.
        
        $history = WorkOrder::whereNotNull('finished_date')
                    ->whereNotNull('taken_date')
                    ->orderBy('taken_date', 'desc')
                    ->limit(20)
                    ->get();

        return view('finish.index', compact('ready', 'history'));
    }

    public function show($id)
    {
        $order = WorkOrder::with([
            'services', 
            'logs', 
            'picSortirSol', 
            'picSortirUpper', 
            'technicianProduction', 
            'qcJahitTechnician', 
            'qcCleanupTechnician', 
            'qcFinalPic'
        ])->findOrFail($id);

        return view('finish.show', compact('order'));
    }

    public function pickup(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        
        $order->taken_date = now();
        $order->save();
        
        $order->logs()->create([
            'step' => WorkOrderStatus::SELESAI->value,
            'action' => 'PICKUP',
            'user_id' => $request->user()?->id,
            'description' => 'Customer picked up the shoes.'
        ]);
        
        return back()->with('success', 'Sepatu telah diambil customer.');
    }
}
