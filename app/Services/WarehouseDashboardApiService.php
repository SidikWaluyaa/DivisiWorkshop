<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\StorageRack;
use App\Models\Material;
use App\Models\WorkOrderLog;
use App\Models\Purchase;
use App\Models\Shipping;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarehouseDashboardApiService
{
    /**
     * Get the full warehouse dashboard summary
     */
    public function getWarehouseSummary(Carbon $start, Carbon $end, ?string $search = null)
    {
        return [
            'metrics' => $this->getHeroMetrics($start, $end),
            'qc_analytics' => [
                'trends' => $this->getQcTrends($start, $end),
                'stats' => $this->getQcStats($start, $end),
            ],
            'efficiency' => $this->getEfficiencyStats($start, $end),
            'inventory' => [
                'value' => $this->getInventoryValue(),
                'material_trends' => $this->getMaterialTrends(),
            ],
            'storage' => [
                'heatmap' => $this->getHeatmapData(),
                'status' => $this->getStorageStats(),
            ],
            'queues' => $this->getQueues($search),
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'last_updated' => now()->toIso8601String(),
        ];
    }

    public function getHeroMetrics(Carbon $start, Carbon $end)
    {
        $qcData = $this->getQcTrends($start, $end);
        
        return [
            // Current Status (Snapshots)
            'pending_reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->count(),
            'finished_not_stored' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereNull('taken_date')
                ->whereDoesntHave('storageAssignments', fn($q) => $q->stored())
                ->count(),
            'stored_items' => StorageRack::active()->sum('current_count'), // Sepatu di rak
            'shipping_pending' => Shipping::where('is_verified', false)->count(),
            'ready_for_pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereNull('taken_date')
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->count(),
            
            // Period Performance (Follow Date Filter)
            'incoming_day' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)
                ->whereBetween('updated_at', [$start, $end])
                ->count(),
            'finished_day' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereBetween('updated_at', [$start, $end])
                ->count(),
            'spk_print' => ($qcData['total_lolos'] ?? 0) - ($qcData['total_reject'] ?? 0),
            
            // Analytics
            'total_incoming' => WorkOrder::whereBetween('created_at', [$start, $end])->count(),
            'total_finished' => WorkOrder::where('status', WorkOrderStatus::SELESAI)->whereBetween('updated_at', [$start, $end])->count(),
            'total_qc_processed' => WorkOrder::whereNotNull('warehouse_qc_status')->whereBetween('warehouse_qc_at', [$start, $end])->count(),
        ];
    }

    public function getQcTrends(Carbon $start, Carbon $end)
    {
        $lolosTrends = WorkOrder::where('warehouse_qc_status', 'lolos')
            ->whereNotNull('warehouse_qc_at')
            ->whereBetween('warehouse_qc_at', [$start, $end])
            ->select(DB::raw('DATE(warehouse_qc_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        $rejectTrends = WorkOrder::where('warehouse_qc_status', 'reject')
            ->whereNotNull('warehouse_qc_at')
            ->whereBetween('warehouse_qc_at', [$start, $end])
            ->select(DB::raw('DATE(warehouse_qc_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        $labels = [];
        $lolosData = [];
        $rejectData = [];
        
        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            
            $foundLolos = $lolosTrends->firstWhere('date', $dateStr);
            $lolosData[] = $foundLolos ? $foundLolos->count : 0;
            
            $foundReject = $rejectTrends->firstWhere('date', $dateStr);
            $rejectData[] = $foundReject ? $foundReject->count : 0;
            
            $current->addDay();
        }

        return [
            'labels' => $labels,
            'lolos' => $lolosData,
            'reject' => $rejectData,
            'total_lolos' => array_sum($lolosData),
            'total_reject' => array_sum($rejectData),
        ];
    }

    public function getQcStats(Carbon $start, Carbon $end)
    {
        $stats = WorkOrder::whereIn('warehouse_qc_status', ['lolos', 'reject'])
            ->whereBetween('warehouse_qc_at', [$start, $end])
            ->select('warehouse_qc_status', DB::raw('count(*) as count'))
            ->groupBy('warehouse_qc_status')
            ->get();

        return [
            'labels' => ['QC Lolos', 'QC Reject'],
            'data' => [
                $stats->firstWhere('warehouse_qc_status', 'lolos')->count ?? 0,
                $stats->firstWhere('warehouse_qc_status', 'reject')->count ?? 0,
            ]
        ];
    }

    public function getEfficiencyStats(Carbon $start, Carbon $end)
    {
        $avgDwell = DB::table('storage_assignments')
            ->whereNotNull('retrieved_at')
            ->whereBetween('stored_at', [$start, $end])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, stored_at, retrieved_at)) as avg_hours')
            ->value('avg_hours');

        $processed = WorkOrderLog::whereBetween('created_at', [$start, $end])->count();

        return [
            'avg_dwell_hours' => round($avgDwell ?? 0, 1),
            'total_throughput' => $processed,
            'health_score' => min(100, (max(0, 100 - ($avgDwell ?? 0))))
        ];
    }

    public function getInventoryValue()
    {
        $materials = Material::all();
        $totalValue = $materials->sum(fn($m) => $m->stock * $m->price);
        
        return [
            'total' => $totalValue,
            'by_category' => $materials->groupBy('category')->map(fn($group) => $group->sum(fn($m) => $m->stock * $m->price)),
        ];
    }

    public function getMaterialTrends()
    {
        return [
            'labels' => Material::orderBy('stock', 'desc')->take(5)->pluck('name'),
            'data' => Material::orderBy('stock', 'desc')->take(5)->pluck('stock'),
        ];
    }

    public function getHeatmapData()
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
                    'category' => $rack->category->value
                ];
            });
    }

    public function getStorageStats()
    {
        return [
            'total_capacity' => StorageRack::active()->sum('capacity'),
            'current_usage' => StorageRack::active()->sum('current_count'),
            'available_slots' => StorageRack::active()->get()->sum(fn($r) => $r->capacity - $r->current_count),
        ];
    }

    public function getQueues(?string $search = null)
    {
        return [
            'reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
            'needs_qc' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
            'storage' => WorkOrder::whereIn('status', [WorkOrderStatus::ASSESSMENT, WorkOrderStatus::WAITING_PAYMENT, WorkOrderStatus::SELESAI])
                ->whereDoesntHave('storageAssignments', fn($q) => $q->stored())
                ->where(function($q) {
                    $q->whereNotNull('warehouse_qc_status')->orWhere('status', WorkOrderStatus::SELESAI);
                })
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
            'pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereNull('taken_date')
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->with(['storageAssignments' => fn($q) => $q->stored()])
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
            'shipping_unverified' => Shipping::where('is_verified', false)
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
            'shipping_verified' => Shipping::where('is_verified', true)
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
        ];
    }
}
