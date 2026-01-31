<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\StorageRack;
use App\Models\StorageAssignment;
use App\Models\Material;
use App\Models\WorkOrderLog;
use App\Enums\WorkOrderStatus;
use App\Services\Storage\StorageService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WarehouseDashboardController extends Controller
{
    protected $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');

        // 1. Hero Stats (Always base total, unaffected by search)
        $stats = [
            'pending_reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->count(),
            'needs_processing' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)->count(),
            'stored_items' => StorageAssignment::stored()->count(),
            'ready_for_pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->count(),
        ];

        // 2. Storage Statistics & Overdue
        $storageStats = $this->storageService->getStatistics();
        $overdueItems = $this->storageService->getOverdueItems(7);

        // 3. Rack Utilization
        $rackStats = $this->storageService->getRackUtilization();
        $racksByCategory = StorageRack::active()
            ->orderBy('rack_code')
            ->get()
            ->groupBy('category');

        // 4. Low Stock Materials
        $lowStockMaterials = Material::whereRaw('stock < min_stock')
            ->orderByRaw('(stock / min_stock) ASC')
            ->take(5)
            ->get();

        // 5. Recent Warehouse Activity
        $recentLogs = WorkOrderLog::whereIn('step', ['RECEPTION', 'STORAGE'])
            ->with(['user', 'workOrder'])
            ->latest()
            ->take(10)
            ->get();

        // 6. Action Queues (Filtered by Search)
        $queues = [
            'reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)
                ->when($search, function($q) use ($search) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%"));
                })
                ->latest()
                ->take(50)
                ->get(),
            'needs_qc' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)
                ->when($search, function($q) use ($search) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%"));
                })
                ->latest()
                ->take(50)
                ->get(),
            'storage' => WorkOrder::whereIn('status', [WorkOrderStatus::ASSESSMENT, WorkOrderStatus::WAITING_PAYMENT, WorkOrderStatus::SELESAI])
                ->whereDoesntHave('storageAssignments', fn($q) => $q->stored())
                ->where(function($q) {
                    $q->whereNotNull('warehouse_qc_status')
                      ->orWhere('status', WorkOrderStatus::SELESAI);
                })
                ->when($search, function($q) use ($search) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%"));
                })
                ->latest()
                ->take(50)
                ->get(),
            'pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->with(['storageAssignments' => fn($q) => $q->stored()])
                ->when($search, function($q) use ($search) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%"));
                })
                ->latest()
                ->take(50)
                ->get(),
        ];

        return view('warehouse.dashboard.index', compact(
            'stats',
            'storageStats',
            'overdueItems',
            'rackStats',
            'racksByCategory',
            'lowStockMaterials',
            'recentLogs',
            'queues',
            'search'
        ));
    }
}
