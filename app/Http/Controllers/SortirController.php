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
        
        // Split for Tabbed Interface
        $solMaterials = Material::where('type', 'Material Sol')->orderBy('name')->get();
        $upperMaterials = Material::where('type', 'Material Upper')->orderBy('name')->get();
        
        $techSol = \App\Models\User::where('role', 'technician')->where('specialization', 'PIC Material Sol')->get();
        $techUpper = \App\Models\User::where('role', 'technician')->where('specialization', 'PIC Material Upper')->get();
        
        // Determine Suggested Tab based on Service Category
        $suggestedTab = 'upper'; // Default
        $hasSolService = $order->services->contains(function($service) {
            $cat = strtolower($service->category);
            return str_contains($cat, 'sol') || str_contains($cat, 'midsole') || str_contains($cat, 'paket');
        });
        
        if ($hasSolService) {
            $suggestedTab = 'sol';
        }
        
        return view('sortir.show', compact('order', 'solMaterials', 'upperMaterials', 'techSol', 'techUpper', 'suggestedTab'));
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

    public function addService(Request $request, $id)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $order = WorkOrder::findOrFail($id);
        $service = \App\Models\Service::findOrFail($request->service_id);

        // 1. Attach Service
        $order->services()->attach($service->id);

        // 2. Reset Workflows (Back to PREPARATION)
        // Even if currently in Sortir, adding a service means we might need to do Prep again (e.g. Bongkar for Sol)
        // User confirmed they want "Fast Flow" where Prep timestamps are NOT reset, so it stays "Completed" in Prep 
        // and immediately ready for Sortir/Production.
        $order->status = WorkOrderStatus::PREPARATION->value; 
        
        // 3. Smart Reset Production Timestamps
        $cat = strtolower($service->category);
        if (str_contains($cat, 'sol')) {
            $order->prod_sol_started_at = null;
            $order->prod_sol_completed_at = null;
        }
        if (str_contains($cat, 'upper') || str_contains($cat, 'jahit') || str_contains($cat, 'repaint')) {
            $order->prod_upper_started_at = null;
            $order->prod_upper_completed_at = null;
        }
        if (str_contains($cat, 'cleaning') || str_contains($cat, 'whitening') || str_contains($cat, 'repaint') || str_contains($cat, 'treatment')) {
             $order->prod_cleaning_started_at = null;
             $order->prod_cleaning_completed_at = null;
        }

        $order->save();

        // 4. Log
        $order->logs()->create([
             'step' => WorkOrderStatus::PREPARATION->value,
             'action' => 'UPSELL',
             'user_id' => $request->user()?->id,
             'description' => "Added Service in Sortir: {$service->name}. Order reset to PREPARATION."
        ]);

        // 5. Handle Photo Upload
        if ($request->hasFile('upsell_photo')) {
            $file = $request->file('upsell_photo');
            $filename = 'UPSELL_SORTIR_' . $order->spk_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('photos/upsell', $filename, 'public');

            \App\Models\WorkOrderPhoto::create([
                'work_order_id' => $order->id,
                'step' => 'UPSELL_SORTIR_BEFORE', // Distinct step to identify source
                'file_path' => $path,
                'is_public' => true,
            ]);
        }

        return redirect()->route('sortir.index')->with('success', 'Layanan berhasil ditambahkan. Order kembali ke status Preparation.');
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
