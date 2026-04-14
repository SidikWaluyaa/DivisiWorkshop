<?php

namespace App\Http\Controllers;

use App\Models\MaterialRequest;
use App\Services\MaterialManagementService;
use Illuminate\Http\Request;

class MaterialRequestController extends Controller
{
    protected MaterialManagementService $materialService;

    public function __construct(MaterialManagementService $materialService)
    {
        $this->materialService = $materialService;
    }

    /**
     * Display a listing of material requests
     */
    public function index(Request $request)
    {
        $query = MaterialRequest::with(['requestedBy', 'approvedBy', 'items.material', 'workOrder', 'oto'])
            ->latest();

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhereHas('requestedBy', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->paginate(20)->appends($request->all());

        return view('material-requests.index', compact('requests'));
    }

    /**
     * Display the specified material request
     */
    public function show(MaterialRequest $materialRequest)
    {
        $materialRequest->load(['requestedBy', 'approvedBy', 'items.material', 'workOrder', 'oto']);

        return view('material-requests.show', compact('materialRequest'));
    }

    /**
     * Approve a material request
     */
    public function approve(MaterialRequest $materialRequest)
    {
        // SECURITY
        $this->authorize('manageInventory', \App\Models\WorkOrder::class);

        if (!$materialRequest->isPending()) {
            return redirect()->back()->with('error', 'Hanya request dengan status PENDING yang bisa diapprove.');
        }

        $materialRequest->approve(\Illuminate\Support\Facades\Auth::id());

        return redirect()->back()->with('success', 'Pengajuan material berhasil disetujui.');
    }

    /**
     * Reject a material request
     */
    public function reject(MaterialRequest $materialRequest)
    {
        // SECURITY
        $this->authorize('manageInventory', \App\Models\WorkOrder::class);

        if (!$materialRequest->isPending()) {
            return redirect()->back()->with('error', 'Hanya request dengan status PENDING yang bisa ditolak.');
        }

        $materialRequest->reject();

        return redirect()->back()->with('success', 'Pengajuan material ditolak.');
    }

    /**
     * Mark as purchased/received and update stock
     */
    public function markAsPurchased(MaterialRequest $materialRequest)
    {
        // SECURITY
        $this->authorize('manageInventory', \App\Models\WorkOrder::class);

        // Allow Approved or Pending (if direct purchase)
        if (!$materialRequest->isApproved() && !$materialRequest->isPending()) {
             return redirect()->back()->with('error', 'Request harus berstatus PENDING atau APPROVED.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($materialRequest) {
            // REFRESH to get latest status and LOCK for update
            $materialRequest->refresh();
            
            if ($materialRequest->status === 'PURCHASED') {
                return; // Already processed by someone else
            }

            // 1. Mark status as PURCHASED
            $materialRequest->markAsPurchased();

            // 2. Increment Stock & Log Transaction using Service
            foreach ($materialRequest->items as $item) {
                if ($item->material) {
                    $this->materialService->restock(
                        $item->material,
                        $item->quantity,
                        "Penerimaan barang dari Pengajuan #{$materialRequest->request_number}",
                        'MaterialRequest',
                        $materialRequest->id
                    );
                }
            }

            // 3. SMART FULFILLMENT: Handle allocation per item
            $impactedWorkOrders = [];

            foreach ($materialRequest->items as $item) {
                // Determine target WorkOrder (from item or root request)
                $workOrderId = $item->work_order_id ?? $materialRequest->work_order_id;
                
                if (!$workOrderId || !$item->material) continue;

                $order = \App\Models\WorkOrder::find($workOrderId);
                if (!$order) continue;

                // Check if this material is still requested for this Work Order
                $pivot = $order->materials()->where('material_id', $item->material_id)->first();
                
                if ($pivot && $pivot->pivot->status === 'REQUESTED') {
                    // Automatically ALLOCATED
                    $order->materials()->updateExistingPivot($item->material_id, [
                        'status' => 'ALLOCATED'
                    ]);

                    // Log internal allocation
                    $this->materialService->logTransaction(
                        $item->material,
                        'OUT',
                        $item->quantity,
                        'WorkOrder',
                        $order->id,
                        "Alokasi otomatis dari Penerimaan PO #{$materialRequest->request_number}"
                    );

                    // Decrement available stock
                    $item->material->decrement('stock', $item->quantity);

                    // Track for logging
                    $impactedWorkOrders[$order->id] = $order;
                }
            }

            // 4. Global Auto-Allocation for any leftover stock
            $this->materialService->autoAllocateStock();
        });

        return redirect()->back()->with('success', 'Material ditandai sudah dibeli & Stok otomatis bertambah.');
    }

    /**
     * Cancel a material request
     */
    public function cancel(MaterialRequest $materialRequest)
    {
        if (!$materialRequest->isPending()) {
            return redirect()->back()->with('error', 'Hanya request dengan status PENDING yang bisa dibatalkan.');
        }

        $materialRequest->cancel();

        return redirect()->back()->with('success', 'Pengajuan material dibatalkan.');
    }

    /**
     * Create PO for unavailable materials
     */
    public function createPO(Request $request)
    {
        // SECURITY
        $this->authorize('manageInventory', \App\Models\WorkOrder::class);

        $validated = $request->validate([
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.shortage' => 'required|integer|min:1',
            'materials.*.available_stock' => 'required|integer|min:0',
            'work_order_id' => 'nullable|exists:work_orders,id',
            'oto_id' => 'nullable|exists:otos,id',
            'notes' => 'nullable|string',
        ]);

        try {
            // Prepare items for PO creation
            $items = [];
            foreach ($validated['materials'] as $materialData) {
                $material = \App\Models\Material::find($materialData['material_id']);
                $items[] = [
                    'material' => $material,
                    'shortage' => $materialData['shortage'],
                    'available_stock' => $materialData['available_stock'],
                ];
            }

            $po = $this->materialService->createProductionPO(
                $items,
                $validated['work_order_id'] ?? null,
                $validated['oto_id'] ?? null,
                $validated['notes'] ?? null
            );

            return redirect()->route('material-requests.show', $po)
                ->with('success', "Purchase Order berhasil dibuat: {$po->request_number}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat PO: ' . $e->getMessage());
        }
    }
}
