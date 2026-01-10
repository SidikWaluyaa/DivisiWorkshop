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
                    ->with('services')
                    ->orderBy('finished_date', 'desc')
                    ->get();

        // 2. Taken/Completed History (Last 50 maybe?)
        // If status is still SELESAI but taken_date is filled? 
        // Or we might not change status to 'DIAMBIL' in enum to keep it simple, just check taken_date.
        // Let's assume 'SELESAI' is the status, 'taken_date' marks it gone.
        
        $history = WorkOrder::whereNotNull('taken_date')
                    ->with('services')
                    ->orderBy('taken_date', 'asc')
                    ->orderBy('id', 'asc')
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

        $services = \App\Models\Service::all();
        return view('finish.show', compact('order', 'services'));
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

    public function addService(Request $request, $id)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $order = WorkOrder::findOrFail($id);
        $service = \App\Models\Service::findOrFail($request->service_id);

        // 1. Attach Service
        $order->services()->attach($service->id);

        // 2. Reset Workflows
        $order->status = WorkOrderStatus::PREPARATION->value; // Go back to Prep
        $order->finished_date = null; // Not finished anymore
        $order->taken_date = null; // Not taken anymore (if it was)
        
        // 3. Reset QC (Must be re-verified)
        $order->qc_jahit_started_at = null;
        $order->qc_jahit_completed_at = null;
        $order->qc_cleanup_started_at = null;
        $order->qc_cleanup_completed_at = null;
        $order->qc_final_started_at = null;
        $order->qc_final_completed_at = null;

        // 4. Smart Reset Production
        // based on new service category
        $cat = strtolower($service->category);
        if (str_contains($cat, 'sol')) {
            $order->prod_sol_started_at = null;
            $order->prod_sol_completed_at = null;
        }
        if (str_contains($cat, 'upper') || str_contains($cat, 'jahit') || str_contains($cat, 'repaint')) {
            $order->prod_upper_started_at = null;
            $order->prod_upper_completed_at = null;
        }
        // Repaint often falls into Upper or Treatment depending on shop logic
        // If "Repaint" is its own primary category, it might need both or specific one.
        // Assuming Repaint/Treatment resets usage of "Clean/Deep Clean" queue slots if relevant?
        // Or if Repaint is separate queue? Current queuing puts Repaint in "Treatment/Cleaning" queue logic mostly?
        // Wait, "Repaint & Treatment" is the queue.
        if (str_contains($cat, 'cleaning') || str_contains($cat, 'whitening') || str_contains($cat, 'repaint') || str_contains($cat, 'treatment')) {
             $order->prod_cleaning_started_at = null;
             $order->prod_cleaning_completed_at = null;
        }

        $order->save();

        // 5. Log
        $order->logs()->create([
             'step' => WorkOrderStatus::PREPARATION->value,
             'action' => 'UPSELL',
             'user_id' => $request->user()?->id,
             'description' => "Added Service: {$service->name}. Order reset to PREPARATION."
        ]);

        return redirect()->route('finish.index')->with('success', 'Layanan berhasil ditambahkan. Order kembali ke status Preparation.');
    }
}
