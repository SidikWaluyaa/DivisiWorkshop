<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Material;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\DB;

class SortirController extends Controller
{
    protected WorkflowService $workflow;

    public function __construct(WorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index()
    {
        // Queue: PREPARATION -> SORTIR
        // The previous step (Prep) moves it to SORTIR.
        
        $queue = WorkOrder::where('status', WorkOrderStatus::SORTIR->value)
                    ->with(['services', 'materials'])
                    ->orderBy('updated_at', 'asc')
                    ->get();
                    
        return view('sortir.index', compact('queue'));
    }

    public function show($id)
    {
        $order = WorkOrder::with(['materials', 'services'])->findOrFail($id);
        $allMaterials = Material::orderBy('name')->get();
        
        $techSol = \App\Models\User::where('role', 'technician')->where('specialization', 'PIC Material Sol')->get();
        $techUpper = \App\Models\User::where('role', 'technician')->where('specialization', 'PIC Material Upper')->get();
        
        return view('sortir.show', compact('order', 'allMaterials', 'techSol', 'techUpper'));
    }

    public function addMaterial(Request $request, $id)
    {
        $order = WorkOrder::findOrFail($id);
        
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $material = Material::findOrFail($request->material_id);
        
        // Determine status based on stock
        $status = 'ALLOCATED'; // READY
        if ($material->stock < $request->quantity) {
            $status = 'REQUESTED'; // Need to buy
        } else {
            // Deduct stock immediately? Or wait? 
            // For this simpler flow: Deduct now to reserve.
            $material->decrement('stock', $request->quantity);
        }
        
        $order->materials()->attach($material->id, [
            'quantity' => $request->quantity,
            'status' => $status
        ]);
        
        return back()->with('success', 'Material added.');
    }
    
    public function updateMaterialStatus(Request $request, $orderId, $materialId)
    {
        // Logic to update status manually (e.g. after buying)
        return back();
    }

    public function destroyMaterial($orderId, $materialId)
    {
        $order = WorkOrder::findOrFail($orderId);
        
        // Find existing pivot data to check status
        $material = $order->materials()->where('material_id', $materialId)->first();
        
        if ($material) {
            // Restore stock if it was reserved/allocated
            if ($material->pivot->status == 'ALLOCATED') {
                $libMaterial = Material::find($materialId);
                if ($libMaterial) {
                    $libMaterial->increment('stock', $material->pivot->quantity);
                }
            }
            
            // Detach/Remove
            $order->materials()->detach($materialId);
        }
        
        return back()->with('success', 'Material removed from order.');
    }

    public function finish(Request $request, $id)
    {
        $order = WorkOrder::with('materials')->findOrFail($id);
        
        $request->validate([
            'pic_sortir_sol_id' => 'nullable|exists:users,id',
            'pic_sortir_upper_id' => 'nullable|exists:users,id',
        ]);
        
        // Validation: ensure all added materials are ALLOCATED/READY.
        $pendingMaterials = $order->materials()->wherePivot('status', 'REQUESTED')->count();
        
        if ($pendingMaterials > 0) {
            return back()->with('error', 'Masih ada material yang belum READY (Status: REQUESTED). Silakan belanja dulu.');
        }

        // Save PICs
        $order->update([
            'pic_sortir_sol_id' => $request->pic_sortir_sol_id,
            'pic_sortir_upper_id' => $request->pic_sortir_upper_id,
        ]);

        // Move to PRODUCTION
        $this->workflow->updateStatus($order, WorkOrderStatus::PRODUCTION, 'Material Verified. Ready for Production.');
        
        return redirect()->route('sortir.index')->with('success', 'Material OK. Sepatu masuk Production.');
    }
}
