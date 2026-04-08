<?php

namespace App\Livewire\Warehouse;

use App\Models\WorkOrder;
use App\Models\StorageRack;
use App\Models\StorageAssignment;
use App\Models\Material;
use App\Models\WorkOrderLog;
use App\Models\Purchase;
use App\Models\CxIssue;
use App\Models\Shipping;
use App\Enums\WorkOrderStatus;
use App\Services\Storage\StorageService;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    #[Url]
    public $search = '';

    #[Url(except: 'summary')]
    public $activeTab = 'summary';

    public function render(StorageService $storageService)
    {
        // 1. Hero Stats
        $stats = [
            'pending_reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)
                ->whereDoesntHave('cxAfterConfirmation')
                ->count(),
            'needs_processing' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)
                ->whereDoesntHave('cxAfterConfirmation')
                ->count(),
            'ready_for_pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereNull('taken_date')
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->whereDoesntHave('cxAfterConfirmation')
                ->count(),
            'finished_not_stored' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereNull('taken_date')
                ->whereDoesntHave('storageAssignments', fn($q) => $q->stored())
                ->whereDoesntHave('cxAfterConfirmation')
                ->count(),
            'shipping_pending' => Shipping::where('is_verified', false)
                ->count(),
        ];

        // 2. Storage & Operational Context
        $storageStats = $storageService->getStatistics();
        $overdueItems = $storageService->getOverdueItems(7);

        // 3. Rack Context
        $rackStats = $storageService->getRackUtilization();
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
                ->whereDoesntHave('cxAfterConfirmation')
                ->when($this->search, function($q) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$this->search}%")->orWhere('customer_name', 'LIKE', "%{$this->search}%"));
                })
                ->latest()->take(20)->get(),
            'needs_qc' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)
                ->whereDoesntHave('cxAfterConfirmation')
                ->when($this->search, function($q) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$this->search}%")->orWhere('customer_name', 'LIKE', "%{$this->search}%"));
                })
                ->latest()->take(20)->get(),
            'storage' => WorkOrder::whereIn('status', [WorkOrderStatus::ASSESSMENT, WorkOrderStatus::WAITING_PAYMENT, WorkOrderStatus::SELESAI])
                ->whereDoesntHave('cxAfterConfirmation')
                ->whereDoesntHave('storageAssignments', fn($q) => $q->stored())
                ->where(function($q) {
                    $q->whereNotNull('warehouse_qc_status')->orWhere('status', WorkOrderStatus::SELESAI);
                })
                ->when($this->search, function($q) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$this->search}%")->orWhere('customer_name', 'LIKE', "%{$this->search}%"));
                })
                ->latest()->take(20)->get(),
            'pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereNull('taken_date')
                ->whereDoesntHave('cxAfterConfirmation')
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->with(['storageAssignments' => fn($q) => $q->stored()])
                ->when($this->search, function($q) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$this->search}%")->orWhere('customer_name', 'LIKE', "%{$this->search}%"));
                })
                ->latest()->take(20)->get(),
            'shipping_unverified' => Shipping::where('is_verified', false)
                ->when($this->search, function($q) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$this->search}%")->orWhere('customer_name', 'LIKE', "%{$this->search}%"));
                })
                ->latest()->take(20)->get(),
            'shipping_verified' => Shipping::where('is_verified', true)
                ->when($this->search, function($q) {
                    $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$this->search}%")->orWhere('customer_name', 'LIKE', "%{$this->search}%"));
                })
                ->latest()->take(20)->get(),
        ];

        return view('livewire.warehouse.dashboard', [
            'stats' => $stats,
            'storageStats' => $storageStats,
            'overdueItems' => $overdueItems,
            'rackStats' => $rackStats,
            'racksByCategory' => $racksByCategory,
            'lowStockMaterials' => $lowStockMaterials,
            'recentLogs' => $recentLogs,
            'queues' => $queues,
            'inventoryValue' => $this->inventoryValue,
            'supplierAnalytics' => $this->supplierAnalytics,
            'qcRejectTrends' => $this->qcRejectTrends,
            'qcRejectReasons' => $this->qcRejectReasons,
            'materialTrends' => $this->materialTrends,
            'heatmapData' => $this->heatmapData,
            'efficiencyStats' => $this->efficiencyStats,
            'availableRacks' => StorageRack::active()->available()->get(),
        ])->layout('layouts.app');
    }

    /**
     * Quick Actions
     */
    public function quickStore($workOrderId, $rackCode, StorageService $storageService)
    {
        try {
            $storageService->assignToRack($workOrderId, $rackCode);
            $this->dispatch('refreshCharts');
            $this->dispatch('notify', ['type' => 'success', 'message' => "Order stored to {$rackCode}"]);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function quickRetrieve($workOrderId, StorageService $storageService)
    {
        try {
            $storageService->retrieveFromStorage($workOrderId);
            $this->dispatch('refreshCharts');
            $this->dispatch('notify', ['type' => 'success', 'message' => "Order released from storage"]);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Computed Properties for Visual Intelligence
     */
    #[Computed]
    public function heatmapData()
    {
        return StorageRack::active()
            ->orderBy('rack_code')
            ->get()
            ->map(function($rack) {
                $usage = ($rack->current_count / max(1, $rack->capacity)) * 100;
                return [
                    'code' => $rack->rack_code,
                    'usage' => round($usage),
                    'color' => $usage > 90 ? 'black' : ($usage > 50 ? 'yellow' : 'green'),
                    'count' => $rack->current_count,
                    'capacity' => $rack->capacity,
                    'category' => $rack->category->label()
                ];
            });
    }

    #[Computed]
    public function efficiencyStats()
    {
        // Simple Average Dwell Time (Storage to Retrieval) in last 30 days
        $avgDwell = DB::table('storage_assignments')
            ->whereNotNull('retrieved_at')
            ->where('stored_at', '>=', now()->subDays(30))
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, stored_at, retrieved_at)) as avg_hours')
            ->value('avg_hours');

        // Total processing throughput today
        $todayProcessed = WorkOrderLog::whereDate('created_at', today())->count();

        return [
            'avg_dwell_hours' => round($avgDwell ?? 0, 1),
            'today_throughput' => $todayProcessed,
            'health_score' => min(100, (max(0, 100 - ($avgDwell ?? 0)))) // Purely cosmetic logic
        ];
    }

    #[Computed]
    public function inventoryValue()
    {
        $materials = Material::all();
        $totalValue = $materials->sum(fn($m) => $m->stock * $m->price);
        
        return [
            'total' => $totalValue,
            'by_category' => $materials->groupBy('category')->map(fn($group) => $group->sum(fn($m) => $m->stock * $m->price)),
        ];
    }

    #[Computed]
    public function supplierAnalytics()
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

    #[Computed]
    public function qcRejectTrends()
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

    #[Computed]
    public function qcRejectReasons()
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

    #[Computed]
    public function materialTrends()
    {
        return [
            'labels' => Material::orderBy('stock', 'desc')->take(5)->pluck('name'),
            'data' => Material::orderBy('stock', 'desc')->take(5)->pluck('stock'),
        ];
    }
}
