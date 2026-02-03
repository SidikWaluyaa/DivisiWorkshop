<?php

namespace App\Http\Controllers;

use App\Services\Storage\StorageService;
use App\Models\WorkOrder;
use App\Models\StorageRack;
use App\Models\StorageAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorageController extends Controller
{
    protected StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * Display storage dashboard
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Handle Category Persistence
        if ($request->has('category')) {
            $category = $request->input('category');
            if (!in_array($category, ['shoes', 'accessories', 'before'])) {
                 $category = 'shoes';
            }
            session(['storage_category' => $category]);
        } else {
            $category = session('storage_category', 'shoes');
        }
        
        // Get statistics (filtered)
        $stats = $this->storageService->getStatistics($category);
        $rackUtilization = $this->storageService->getRackUtilization($category);
        
        // Get stored items
        $storedItems = $search 
            ? $this->storageService->search($search, $category)
            : StorageAssignment::with(['workOrder.customer', 'rack'])
                ->where('category', $category) // Direct column filtering
                ->stored()
                ->orderBy('stored_at', 'desc')
                ->paginate(20)
                ->withQueryString();
        
        // Get overdue items (filtered)
        $overdueItems = $this->storageService->getOverdueItems(7, $category);
        
        // Get racks for visualization (Split by category)
        // Get racks for visualization (Split by category) - STRICT MODE
        $shoeRacks = StorageRack::active()->where('category', 'shoes')->orderBy('rack_code')->get();
        $accessoryRacks = StorageRack::active()->where('category', 'accessories')->orderBy('rack_code')->get();
        $beforeRacks = StorageRack::active()->where('category', 'before')->orderBy('rack_code')->get();
        
        return view('storage.index', compact(
            'stats',
            'rackUtilization',
            'storedItems',
            'overdueItems',
            'shoeRacks',
            'accessoryRacks',
            'beforeRacks',
            'search'
        ));
    }

    /**
     * Store work order to rack
     */
    public function store(Request $request)
    {
        // SECURITY
        $this->authorize('manageStorage', WorkOrder::class);

        $validated = $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'rack_code' => 'nullable|exists:storage_racks,rack_code',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Auto-assign if no rack specified
            if (empty($validated['rack_code'])) {
                $rack = $this->storageService->autoAssignRack();
                if (!$rack) {
                    return back()->with('error', 'Tidak ada rak yang tersedia');
                }
                $validated['rack_code'] = $rack->rack_code;
            }

            $assignment = $this->storageService->assignToRack(
                $validated['work_order_id'],
                $validated['rack_code'],
                $validated['notes'] ?? null
            );

            return back()->with('success', "Sepatu berhasil disimpan di rak {$validated['rack_code']}");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Retrieve work order from storage
     */
    public function retrieve(int $id, Request $request)
    {
        // SECURITY
        $this->authorize('manageStorage', WorkOrder::class);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $assignment = StorageAssignment::findOrFail($id);
            
            $this->storageService->retrieveFromStorage(
                $assignment->work_order_id,
                $validated['notes'] ?? null
            );

            return back()->with('success', 'Sepatu berhasil diambil dari gudang');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Unassign/Undo storage
     */
    public function unassign(int $id, Request $request)
    {
        // SECURITY
        $this->authorize('manageStorage', WorkOrder::class);

        try {
            $this->storageService->unassignFromRack($id);
            return back()->with('success', 'Tag penyimpanan berhasil dilepas. Item kembali ke status Menunggu Disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show storage details
     */
    public function show(int $id)
    {
        $assignment = StorageAssignment::with(['workOrder.customer', 'rack', 'storedByUser', 'retrievedByUser'])
            ->findOrFail($id);

        return view('storage.show', compact('assignment'));
    }

    /**
     * Search stored items
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->route('storage.index');
        }

        $results = $this->storageService->search($query);

        return view('storage.search', compact('results', 'query'));
    }

    /**
     * Print storage label
     * Expects $id to be work_order_id when called from Finish page
     */
    public function printLabel(int $id)
    {
        // Try to find by ID first (legacy support), if not found or if caller was passing WO ID, try finding by WO ID
        // Actually, to be safe and consistent with the Finish page usage:
        // We will assume $id is work_order_id because that's what the UI sends.
        
        $assignment = StorageAssignment::where('work_order_id', $id)
            ->where('status', 'stored')
            ->with(['workOrder.customer', 'rack'])
            ->latest() // Get the latest one if multiple exist (though should be unique 'stored')
            ->first();

        // Fallback: If not found by WO ID, maybe it passed Assignment ID directly (e.g. from Storage Dashboard)
        if (!$assignment) {
             $assignment = StorageAssignment::with(['workOrder.customer', 'rack'])
                ->find($id);
        }

        if (!$assignment) {
            abort(404, 'Storage assignment not found for this Order.');
        }

        return view('storage.label', compact('assignment'));
    }

    /**
     * Print shipping label (Custom Design)
     */
    public function printShippingLabel(int $id)
    {
        // Same logic: Attempt to find valid active assignment for this WO ID
        $assignment = StorageAssignment::where('work_order_id', $id)
            ->where('status', 'stored')
            ->with(['workOrder.customer'])
            ->latest()
            ->first();

        // Fallback or explicit check
        if (!$assignment) {
             $assignment = StorageAssignment::with(['workOrder.customer'])
                ->find($id);
        }
        
        if (!$assignment) {
            abort(404, 'Storage assignment not found.');
        }

        $order = $assignment->workOrder;
        return view('admin.orders.shipping-label', compact('order'));
    }

    /**
     * Get available racks (AJAX)
     */
    public function availableRacks(Request $request)
    {
        $location = $request->input('location');
        $racks = $this->storageService->getAvailableRacks($location);

        return response()->json($racks);
    }

    /**
     * Get rack contents (AJAX)
     */
    public function rackDetails(string $rackCode, Request $request)
    {
        $category = $request->input('category');
        
        $items = StorageAssignment::where('rack_code', $rackCode)
            ->where('category', $category)
            ->stored()
            ->stored()
            ->with(['workOrder' => function($q) {
                $q->select('id', 'spk_number', 'shoe_brand', 'shoe_size', 'shoe_color', 'accessories_tali', 'accessories_insole', 'accessories_box', 'accessories_other');
            }])
            ->get();

        return response()->json([
            'rack_code' => $rackCode,
            'category' => $category,
            'items' => $items->map(function($item) {
                return [
                    'id' => $item->id,
                    'spk_number' => $item->workOrder?->spk_number ?? 'N/A',
                    'item_info' => $item->workOrder ? 
                        "{$item->workOrder->shoe_brand} ({$item->workOrder->shoe_color}, {$item->workOrder->shoe_size})" : '-',
                    'accessories' => $item->workOrder ? [
                        'tali' => $item->workOrder->accessories_tali,
                        'insole' => $item->workOrder->accessories_insole,
                        'box' => $item->workOrder->accessories_box,
                        'other' => $item->workOrder->accessories_other
                    ] : null,
                    'stored_at' => $item->stored_at->format('d M Y H:i')
                ];
            })
        ]);
    }

    /**
     * Bulk Retrieve items from storage
     */
    public function bulkRetrieve(Request $request)
    {
        $this->authorize('manageStorage', WorkOrder::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:storage_assignments,id',
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($request->ids as $id) {
            try {
                $assignment = StorageAssignment::findOrFail($id);
                $this->storageService->retrieveFromStorage($assignment->work_order_id);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID {$id}: " . $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            return back()->with('warning', "Berhasil mengambil {$successCount} item. Gagal pada: " . implode(', ', $errors));
        }

        return back()->with('success', "Berhasil mengambil {$successCount} item dari gudang.");
    }

    /**
     * Bulk Unassign items from storage
     */
    public function bulkUnassign(Request $request)
    {
        // Unassign usually takes work_order_id in its current service implementation
        $this->authorize('manageStorage', WorkOrder::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:storage_assignments,id',
        ]);

        $successCount = 0;

        foreach ($request->ids as $id) {
            try {
                // Unassign usually takes work_order_id in its current service implementation
                $assignment = StorageAssignment::findOrFail($id);
                $this->storageService->unassignFromRack($assignment->work_order_id);
                $successCount++;
            } catch (\Exception $e) {
                // Skip errors for bulk
            }
        }

        return back()->with('success', "Berhasil melepas tag pada {$successCount} item.");
    }

    /**
     * Bulk Delete selected storage assignments (Hard Delete link)
     */
    public function bulkDestroySelection(Request $request)
    {
        $this->authorize('manageStorage', WorkOrder::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:storage_assignments,id',
        ]);

        $ids = $request->ids;

        try {
            DB::beginTransaction();
            
            // Get racks to recalculate later
            $rackCodes = StorageAssignment::whereIn('id', $ids)->pluck('rack_code')->unique();

            // Delete the assignments
            StorageAssignment::whereIn('id', $ids)->delete();

            // Recalculate each impacted rack
            foreach ($rackCodes as $code) {
                $this->storageService->recalculateRackCount($code);
            }

            DB::commit();
            return back()->with('success', count($ids) . ' data penugasan rak berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
