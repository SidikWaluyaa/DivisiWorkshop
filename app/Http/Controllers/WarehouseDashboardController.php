<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\StorageRack;
use App\Models\StorageAssignment;
use App\Models\Material;
use App\Models\WorkOrderLog;
use App\Models\Purchase;
use App\Models\CxIssue;
use App\Enums\WorkOrderStatus;
use App\Services\Storage\StorageService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        ))->with([
            'inventoryValue' => $this->getInventoryValue(),
            'supplierAnalytics' => $this->getSupplierAnalytics(),
            'qcRejectTrends' => $this->getQCRejectTrends(),
            'qcRejectReasons' => $this->getQCRejectReasons(),
            'materialTrends' => $this->getMaterialTrends(),
        ]);
    }

    /**
     * API endpoint for realtime polling (JSON).
     */
    public function apiStats()
    {
        $stats = [
            'pending_reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->count(),
            'needs_processing' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)->count(),
            'stored_items' => StorageAssignment::stored()->count(),
            'ready_for_pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->count(),
        ];

        $rackStats = $this->storageService->getRackUtilization();

        $lowStockMaterials = Material::whereRaw('stock < min_stock')
            ->orderByRaw('(stock / min_stock) ASC')
            ->take(5)
            ->get()
            ->map(fn($m) => [
                'name' => $m->name,
                'stock' => $m->stock,
                'min_stock' => $m->min_stock,
                'unit' => $m->unit,
            ]);

        return response()->json([
            'stats' => $stats,
            'rack_stats' => [
                'utilization_percentage' => $rackStats['utilization_percentage'],
                'total_available' => $rackStats['total_available'],
                'full_racks' => $rackStats['full_racks'],
            ],
            'low_stock_materials' => $lowStockMaterials,
            'queue_counts' => [
                'reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->count(),
                'needs_qc' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)->count(),
                'storage' => WorkOrder::whereIn('status', [WorkOrderStatus::ASSESSMENT, WorkOrderStatus::WAITING_PAYMENT, WorkOrderStatus::SELESAI])
                    ->whereDoesntHave('storageAssignments', fn($q) => $q->stored())
                    ->where(function($q) {
                        $q->whereNotNull('warehouse_qc_status')
                          ->orWhere('status', WorkOrderStatus::SELESAI);
                    })
                    ->count(),
                'pickup' => $stats['ready_for_pickup'],
            ],
            'timestamp' => now()->format('H:i:s'),
            'inventory_value' => $this->getInventoryValue()['total'],
            'qc_reject_count' => CxIssue::where('source', 'GUDANG')->count(),
        ]);
    }

    private function getInventoryValue()
    {
        $materials = Material::all();
        $totalValue = $materials->sum(fn($m) => $m->stock * $m->price);
        
        return [
            'total' => $totalValue,
            'by_category' => $materials->groupBy('category')->map(fn($group) => $group->sum(fn($m) => $m->stock * $m->price)),
        ];
    }

    private function getSupplierAnalytics()
    {
        $topBySpend = Purchase::whereNotNull('supplier_name')
            ->select('supplier_name', DB::raw('SUM(total_price) as total_spend'))
            ->groupBy('supplier_name')
            ->orderByDesc('total_spend')
            ->take(5)
            ->get();

        $topByRating = Purchase::whereNotNull('supplier_name')
            ->whereNotNull('quality_rating')
            ->select('supplier_name', DB::raw('AVG(quality_rating) as avg_rating'))
            ->groupBy('supplier_name')
            ->orderByDesc('avg_rating')
            ->take(5)
            ->get();

        return [
            'bySpend' => [
                'labels' => $topBySpend->pluck('supplier_name'),
                'data' => $topBySpend->pluck('total_spend'),
            ],
            'byRating' => [
                'labels' => $topByRating->pluck('supplier_name'),
                'data' => $topByRating->map(fn($item) => round($item->avg_rating, 1)),
            ]
        ];
    }

    private function getQCRejectTrends()
    {
        $startDate = now()->subDays(30);
        $trends = CxIssue::where('source', 'GUDANG')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];
        $current = $startDate->copy();
        while ($current <= now()) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            $found = $trends->firstWhere('date', $dateStr);
            $data[] = $found ? $found->count : 0;
            $current->addDay();
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'total' => array_sum($data),
        ];
    }

    private function getQCRejectReasons()
    {
        $reasons = CxIssue::where('source', 'GUDANG')
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        return [
            'labels' => $reasons->pluck('category'),
            'data' => $reasons->pluck('count'),
        ];
    }

    private function getMaterialTrends()
    {
        return [
            'labels' => Material::orderBy('stock', 'desc')->take(5)->pluck('name'),
            'data' => Material::orderBy('stock', 'desc')->take(5)->pluck('stock'),
        ];
    }
}
