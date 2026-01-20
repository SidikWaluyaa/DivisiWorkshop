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

    public function index(Request $request)
    {
        // Base Query
        $validStatuses = [WorkOrderStatus::SORTIR->value];

        // 1. PRIORITAS Queue (Fetch ALL, no pagination)
        $prioritas = WorkOrder::whereIn('status', $validStatuses)
                        ->where('priority', 'Prioritas')
                        ->with(['services', 'materials', 'cxIssues'])
                        ->orderBy('id', 'asc') // FIFO
                        ->get();

        // 2. REGULER Queue (Paginated)
        $regulerQuery = WorkOrder::whereIn('status', $validStatuses)
                        ->where(function($q) {
                             $q->where('priority', '!=', 'Prioritas')
                               ->orWhereNull('priority');
                        });

        // Search Filter (Apply to both? Or just Reguler? Usually both.)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            // Filter Prioritas in memory or query? Query is better but we already got it.
            // Let's apply filter to query BEFORE getting.
            
            // Re-query logic
            $prioritas = WorkOrder::whereIn('status', $validStatuses)
                        ->where('priority', 'Prioritas')
                        ->where(function($q) use ($search) {
                            $q->where('spk_number', 'like', "%{$search}%")
                              ->orWhere('customer_name', 'like', "%{$search}%");
                        })
                        ->with(['services', 'materials', 'cxIssues'])
                        ->orderBy('id', 'asc')
                        ->get();

            $regulerQuery->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $reguler = $regulerQuery->with(['services', 'materials', 'cxIssues'])
                       ->orderBy('id', 'asc') // FIFO
                       ->paginate(20)
                       ->appends($request->all());
                
        return view('sortir.index', compact('prioritas', 'reguler'));
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

    public function updateMaterials(Request $request, $id)
    {
        $order = WorkOrder::with('materials')->findOrFail($id);
        
        $request->validate([
            'materials' => 'array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function() use ($request, $order) {
                // 1. Get current material IDs and Quantities
                $currentMaterials = $order->materials->keyBy('id');
                $newMaterials = collect($request->materials ?? [])->keyBy('material_id');
                
                // 2. Handle Removals (Exist in Current, not in New)
                foreach ($currentMaterials as $matId => $mat) {
                    if (!$newMaterials->has($matId)) {
                        // Restore Stock if it was ALLOCATED
                        if ($mat->pivot->status == 'ALLOCATED') {
                            $mat->increment('stock', $mat->pivot->quantity);
                        }
                        $order->materials()->detach($matId);
                    }
                }

                // 3. Handle Additions & Updates
                foreach ($newMaterials as $matId => $data) {
                    $newQty = (int) $data['quantity'];
                    
                    if ($currentMaterials->has($matId)) {
                        // Update Existing
                        $currentMat = $currentMaterials->get($matId);
                        $oldQty = (int) $currentMat->pivot->quantity;
                        $diff = $newQty - $oldQty;
                        
                        if ($diff != 0) {
                            $currentMatModel = Material::find($matId);
                            // Adjust Stock
                            // If diff is positive (added more), stock decreases.
                            // If diff is negative (reduced qty), stock increases.
                            if ($currentMat->pivot->status == 'ALLOCATED') {
                                $currentMatModel->decrement('stock', $diff);
                            }
                            
                            $order->materials()->updateExistingPivot($matId, ['quantity' => $newQty]);
                        }
                    } else {
                        // New Addition
                        $material = Material::find($matId);
                        $status = 'ALLOCATED'; // Default
                        
                        // Check stock available
                        if ($material->stock < $newQty) {
                             $status = 'REQUESTED'; // Not enough stock
                        } else {
                             $material->decrement('stock', $newQty);
                        }
                        
                        $order->materials()->attach($matId, [
                            'quantity' => $newQty,
                            'status' => $status
                        ]);
                    }
                }
                
                // Recalculate Totals
                $totalMaterialCost = 0;
                foreach ($order->materials()->get() as $m) {
                    $totalMaterialCost += ($m->price * $m->pivot->quantity);
                }
                // Only update total_amount_due if needed, but usually we just update partials
                // or user updates total later. For now let's just save.
            });
            
            return back()->with('success', 'Daftar material berhasil diupdate.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update material: ' . $e->getMessage());
        }
    }

    public function addService(Request $request, $id)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'custom_name' => 'nullable|string|max:255',
        ]);

        $order = WorkOrder::findOrFail($id);
        $service = \App\Models\Service::findOrFail($request->service_id);

        // 1. Attach Service with Cost
        // If custom price is provided (for Rp 0 services), use it. Otherwise use master price.
        $cost = $request->custom_price ?? $service->price;
        
        $pivotData = ['cost' => $cost];
        if ($request->filled('custom_name')) {
            $pivotData['custom_name'] = $request->custom_name;
        }

        $order->services()->attach($service->id, $pivotData);

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
        
        try {
             // Save PICs
            $order->update([
                'pic_sortir_sol_id' => $request->pic_sortir_sol_id,
                'pic_sortir_upper_id' => $request->pic_sortir_upper_id,
            ]);

            // Move to PRODUCTION via Service (Validates Material Ready)
            $this->workflow->updateStatus($order, WorkOrderStatus::PRODUCTION, 'Material Verified. Ready for Production.');
            
            return redirect()->route('sortir.index')->with('success', 'Material OK. Sepatu masuk Production.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_orders,id',
            'action' => 'required|string'
        ]);

        $ids = $request->ids;
        $successCount = 0;
        $failCount = 0;

        foreach ($ids as $id) {
            try {
                $order = WorkOrder::with('materials')->findOrFail($id);
                
                // For Sortir, bulk 'finish' moves them to PRODUCTION
                // Note: We might want to check if materials are ready, 
                // but usually bulk finish implies the user has confirmed they are ready.
                
                $this->workflow->updateStatus($order, WorkOrderStatus::PRODUCTION, 'Material Verified (Bulk). Ready for Production.');
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Proses massal selesai. Berhasil: $successCount, Gagal: $failCount"
        ]);
    }
}
