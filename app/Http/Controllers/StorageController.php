<?php

namespace App\Http\Controllers;

use App\Services\Storage\StorageService;
use App\Models\WorkOrder;
use App\Models\StorageRack;
use App\Models\StorageAssignment;
use Illuminate\Http\Request;

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
            if (!in_array($category, ['shoes', 'accessories'])) {
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
            ? $this->storageService->search($search)
            : StorageAssignment::with(['workOrder.customer', 'rack'])
                ->whereHas('rack', function($q) use ($category) {
                    $q->where('category', $category);
                })
                ->stored()
                ->orderBy('stored_at', 'desc')
                ->paginate(20)
                ->withQueryString();
        
        // Get overdue items (filtered)
        $overdueItems = $this->storageService->getOverdueItems(7, $category);
        
        // Get racks for visualization (Split by category)
        $shoeRacks = StorageRack::active()->where('category', 'shoes')->orderBy('rack_code')->get();
        $accessoryRacks = StorageRack::active()->where('category', 'accessories')->orderBy('rack_code')->get();
        
        // Keep $racks for backwards compatibility if needed, or remove if view is fully updated.
        // For distinct separate grids, we will pass explicit variables.

        return view('storage.index', compact(
            'stats',
            'rackUtilization',
            'storedItems',
            'overdueItems',
            'shoeRacks',
            'accessoryRacks',
            'search'
        ));
    }

    /**
     * Store work order to rack
     */
    public function store(Request $request)
    {
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
        // $id here could be assignment ID or WorkOrder ID depending on route.
        // Route is typically /storage/{id}/unassign.
        // If coming from Finish page, we usually have WorkOrder ID.
        // If coming from Assignment table, we have Assignment ID.
        // StorageService unassignFromRack takes WorkOrderId.
        
        // Let's assume ID is WorkOrderId if route is 'finish/unassign/{id}'?
        // Or better: Route::post('/{id}/unassign', ...).
        // Let's check logic: Service expects WorkOrderId.
        // If $id is Assignment ID, we can get WO ID.
        // Let's support both or stick to one.
        // Finish page lists WorkOrders. Easier to pass WorkOrder ID.
        // Storage Dashboard lists Assignments.
        
        // Let's assume $id is WorkOrder ID for simplicity on Finish Page.
        // Wait, 'retrieve' took Assignment ID. Consistency is key.
        // But in Finish Page I had to lookup Assignment ID.
        // Let's make 'unassign' take WorkOrder ID because "Lepas Tag" is conceptually on the Item.
        
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
     */
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
}
