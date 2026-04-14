<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Material;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\DB;
use App\Services\MaterialManagementService;

class SortirController extends Controller
{
    protected WorkflowService $workflow;
    protected MaterialManagementService $materialService;

    public function __construct(WorkflowService $workflow, MaterialManagementService $materialService)
    {
        $this->workflow = $workflow;
        $this->materialService = $materialService;
    }

    public function index(Request $request)
    {
        // 1. SIAP PRODUKSI Queue (Ready for Production)
        $readyQuery = WorkOrder::readyForProduction()
            ->with(['customer', 'services', 'materials', 'cxIssues']);

        // 2. WAITING LIST Queue (Waiting for Materials WITH active PO)
        $waitingQuery = WorkOrder::waitingForMaterials()
            ->with(['customer', 'services', 'materials', 'cxIssues']);

        // 3. NEEDS REQUEST Queue (Missing materials WITHOUT PO)
        $needsRequestQuery = WorkOrder::needsMaterialRequest()
            ->with(['customer', 'services', 'materials', 'cxIssues']);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $filter = function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            };
            $readyQuery->where($filter);
            $waitingQuery->where($filter);
            $needsRequestQuery->where($filter);
        }

        // Sorting: Priority First, then FIFO
        $orderBy = "CASE 
            WHEN priority IN ('Prioritas', 'Urgent', 'Express') THEN 1 
            ELSE 2 
        END ASC, id ASC";

        $readyOrders = $readyQuery->orderByRaw($orderBy)->paginate(50, ['*'], 'ready_page')->appends($request->all());
        $waitingOrders = $waitingQuery->orderByRaw($orderBy)->paginate(50, ['*'], 'waiting_page')->appends($request->all());
        $needsRequestOrders = $needsRequestQuery->orderByRaw($orderBy)->paginate(50, ['*'], 'needs_page')->appends($request->all());
                
        return view('sortir.index', compact('readyOrders', 'waitingOrders', 'needsRequestOrders'));
    }

    public function show($id)
    {
        $order = WorkOrder::with(['customer', 'materials', 'services'])->findOrFail($id);
        $this->authorize('updateSortir', $order);

        // --- SELF-HEALING: Check for REQUESTED materials that now have stock ---
        $allocatedAny = false;
        foreach ($order->materials as $mat) {
            if ($mat->pivot->status === 'REQUESTED') {
                $freshMat = Material::find($mat->id);
                if ($freshMat && $freshMat->stock >= $mat->pivot->quantity) {
                    // Auto Allocate using Service
                    DB::transaction(function() use ($order, $freshMat, $mat) {
                        $this->materialService->logTransaction(
                            $freshMat, 
                            'OUT', 
                            $mat->pivot->quantity, 
                            'WorkOrder', 
                            $order->id, 
                            "Auto-allocated material from stock for SPK #{$order->spk_number}"
                        );
                        $freshMat->decrement('stock', $mat->pivot->quantity);
                        $order->materials()->updateExistingPivot($mat->id, ['status' => 'ALLOCATED']);
                    });
                    $allocatedAny = true;
                }
            }
        }
        
        if ($allocatedAny) {
            // Refresh relation
            $order->load(['materials', 'services']);
            session()->flash('success', 'Stok tersedia! Material otomatis dialokasikan.');
        }
        // -----------------------------------------------------------------------
        
        // Split for Tabbed Interface
        $solMaterials = Material::where('category', 'PRODUCTION')
            ->where('type', 'Material Sol')
            ->orderBy('name')->get();
            
        $upperMaterials = Material::where('category', 'PRODUCTION')
            ->where('type', 'Material Upper')
            ->orderBy('name')->get();

        $otherMaterials = Material::where('category', 'SHOPPING')
            ->orWhere(function($query) {
                $query->where('category', 'PRODUCTION')
                      ->whereNotIn('type', ['Material Sol', 'Material Upper']);
            })
            ->orderBy('name')->get();

        $techSol = \App\Models\User::where('role', 'pic')->get();
        $techUpper = \App\Models\User::where('role', 'pic')->get();
        
        // Determine Suggested Tab based on Service Category
        $suggestedTab = 'upper'; // Default
        $hasSolService = $order->services->contains(function($service) {
            $cat = strtolower($service->category);
            return str_contains($cat, 'sol') || str_contains($cat, 'midsole') || str_contains($cat, 'paket');
        });
        
        if ($hasSolService) {
            $suggestedTab = 'sol';
        }
        
        return view('sortir.show', compact('order', 'solMaterials', 'upperMaterials', 'otherMaterials', 'techSol', 'techUpper', 'suggestedTab'));
    }

    public function updateMaterials(Request $request, $id)
    {
        $order = WorkOrder::with('materials')->findOrFail($id);
        $this->authorize('updateSortir', $order);
        
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
                        $order->materials()->updateExistingPivot($matId, ['quantity' => $newQty]);
                        
                        // Self-healing: Upgrade to ALLOCATED if it was REQUESTED and now we have stock
                        // Note: We don't decrement yet as per new requirement
                        if ($currentMat->pivot->status === 'REQUESTED') {
                             $material = Material::find($matId);
                             if ($material->stock >= $newQty) {
                                  $order->materials()->updateExistingPivot($matId, ['status' => 'ALLOCATED']);
                             }
                        }
                    } else {
                        // New Addition
                        $material = Material::find($matId);
                        $status = ($material->stock >= $newQty) ? 'ALLOCATED' : 'REQUESTED';
                        
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
        $order = WorkOrder::findOrFail($id);
        $this->authorize('updateSortir', $order);

        // Flexible validation to allow 'custom' service_id
        $request->validate([
            'service_id' => 'required', 
            'custom_name' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        // Determine Service Data
        $serviceId = null;
        $cost = 0;
        $category = 'Custom';
        $name = 'Custom Service';
        $customName = null;

        if ($request->service_id === 'custom') {
            $request->validate([
                'custom_name' => 'required|string|max:255',
                'custom_price' => 'required|numeric|min:0',
                'category' => 'required|string|max:100',
            ]);

            $serviceId = null;
            $name = $request->custom_name; // For logging/display
            $customName = $request->custom_name;
            $cost = $request->custom_price;
            $category = $request->category;
        } else {
            $request->validate([
                'service_id' => 'exists:services,id',
            ]);
            
            $service = \App\Models\Service::findOrFail($request->service_id);
            $serviceId = $service->id;
            $name = $service->name;
            $category = $service->category;
            $cost = $request->custom_price ?? $service->price; // Allow override if needed, otherwise master price

            if ($request->filled('custom_name')) {
                $customName = $request->custom_name;
            }
        }

        // Create WorkOrderService record (using hasMany for flexibility with null service_id)
        $order->workOrderServices()->create([
            'service_id' => $serviceId,
            'cost' => $cost,
            'category_name' => $category,
            'custom_service_name' => $customName,
            'notes' => $request->notes,
            'status' => 'pending' // Default status
        ]);

        // 2. Reset Workflows (Back to PREPARATION)
        $order->status = WorkOrderStatus::PREPARATION; 
        
        // 3. Smart Reset Production Timestamps based on Category
        $cat = strtolower($category);
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
             'description' => "Added Service in Sortir: {$name} ({$category}). Order reset to PREPARATION."
        ]);

        // 5. Handle Photo Upload
        if ($request->hasFile('upsell_photo')) {
            $file = $request->file('upsell_photo');
            $filename = 'UPSELL_SORTIR_' . $order->spk_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('photos/upsell', $filename, 'public');

            \App\Models\WorkOrderPhoto::create([
                'work_order_id' => $order->id,
                'step' => 'UPSELL_SORTIR_BEFORE', 
                'file_path' => $path,
                'is_public' => true,
            ]);
        }

        return redirect()->route('sortir.index')->with('success', 'Layanan berhasil ditambahkan. Order kembali ke status Preparation.');
    }

    public function finish(Request $request, $id)
    {
        $order = WorkOrder::with('materials')->findOrFail($id);
        $this->authorize('updateSortir', $order);
        
        $request->validate([
            'pic_sortir_sol_id' => 'nullable|exists:users,id',
            'pic_sortir_upper_id' => 'nullable|exists:users,id',
        ]);
        
        try {
             // 1. Deduct Materials officially (Record as OUT / Barang Keluar)
             $this->materialService->deductWorkOrderMaterials($order);

             // 2. Save PICs
            $order->update([
                'pic_sortir_sol_id' => $request->pic_sortir_sol_id,
                'pic_sortir_upper_id' => $request->pic_sortir_upper_id,
            ]);

            // Default next status
            $nextStatus = WorkOrderStatus::PRODUCTION;
            $note = 'Material Verified & Consumption Recorded. Ready for Production.';

            // Boomerang Logic: If in revision, jump back to previous status
            if ($order->is_revising && $order->previous_status instanceof WorkOrderStatus) {
                $targetStatus = $order->previous_status;
                $statusLabel = $targetStatus->value;
                $note = "Revision completed in Sortir. Returning to " . $statusLabel;
                
                $this->workflow->updateStatus($order, $targetStatus, $note);

                // Clear revision flags
                $order->is_revising = false;
                $order->previous_status = null;
                $order->save();
                
                return redirect()->route('sortir.index')->with('success', 'Revisi selesai. Sepatu kembali ke ' . $statusLabel);
            } 
            
            if ($order->is_revising) {
                // Fallback for is_revising without previous_status
                $order->is_revising = false;
                $order->save();
            }

            $this->workflow->updateStatus($order, WorkOrderStatus::PRODUCTION, 'Material Verified. Ready for Production.');
            
            return redirect()->route('sortir.index')->with('success', 'Material OK. Sepatu masuk Production.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Skip Sortir checks and move manually to Production
     */
    public function skipToProduction($id)
    {
        $order = WorkOrder::findOrFail($id);
        $this->authorize('updateSortir', $order);
        
        // Strict Check: Only Admin/Owner/Manager
        if (!in_array(\Illuminate\Support\Facades\Auth::user()->role, ['admin', 'owner', 'production_manager'])) {
            abort(403, 'Unauthorized action. Only Admin/Manager can skip Sortir.');
        }

        try {
            $oldStatus = $order->status;

            // Manual Update - Bypass Workflow Check
            // We assume admin knows what they are doing (skipping material input)
            DB::transaction(function () use ($order, $oldStatus) {
                $order->status = WorkOrderStatus::PRODUCTION;
                $order->current_location = 'Rumah Abu'; // Production Area
                $order->save();

                // Dispatch Event anyway so logs are kept
                // We fake the old status object if needed, or pass string
                \App\Events\WorkOrderStatusUpdated::dispatch(
                    $order, 
                    $oldStatus, 
                    WorkOrderStatus::PRODUCTION, 
                    'Direct to Production (Skip Material Check)', 
                    \Illuminate\Support\Facades\Auth::id()
                );
            });

            return redirect()->back()->with('success', 'Order berhasil dikirim langsung ke Production!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses order: ' . $e->getMessage());
        }
    }

    /**
     * Bulk Skip to Production - Direct without material check
     */
    public function bulkSkipToProduction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_orders,id'
        ]);

        $successCount = 0;
        $failCount = 0;

        foreach ($request->ids as $id) {
            try {
                $order = WorkOrder::findOrFail($id);
                $this->authorize('updateSortir', $order);
                $oldStatus = $order->status;

                DB::transaction(function () use ($order, $oldStatus) {
                    $order->status = WorkOrderStatus::PRODUCTION;
                    $order->current_location = 'Rumah Abu';
                    $order->save();

                    \App\Events\WorkOrderStatusUpdated::dispatch(
                        $order, 
                        $oldStatus, 
                        WorkOrderStatus::PRODUCTION, 
                        'Direct to Production (Bulk Skip Material Check)', 
                        \Illuminate\Support\Facades\Auth::id()
                    );
                });

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
                
                // Security check
                if ($order->status !== WorkOrderStatus::SORTIR->value) {
                    $failCount++;
                    continue;
                }

                DB::transaction(function () use ($order) {
                    // 1. Deduct materials from stock officially
                    $this->materialService->deductWorkOrderMaterials($order);

                    // 2. Update status and log
                    $this->workflow->updateStatus(
                        $order, 
                        WorkOrderStatus::PRODUCTION, 
                        'Material Verified & Stock Consumption Recorded (Bulk). Ready for Production.'
                    );
                });

                $successCount++;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Bulk Sortir Finish Error: " . $e->getMessage());
                $failCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Proses massal selesai. Berhasil: $successCount, Gagal: $failCount"
        ]);
    }
    /**
     * One-click trigger to generate MaterialRequest for missing items
     */
    public function requestMaterial($id)
    {
        $order = WorkOrder::findOrFail($id);
        $this->authorize('updateSortir', $order);

        try {
            $request = $this->materialService->requestMissingMaterialsForWorkOrder($order);

            if ($request) {
                return back()->with('success', "Material Request #{$request->request_number} berhasil dibuat dan dikirim ke Purchasing.");
            }

            return back()->with('info', "Tidak ada material yang perlu direquest untuk SPK ini.");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal membuat request: " . $e->getMessage());
        }
    }
}
