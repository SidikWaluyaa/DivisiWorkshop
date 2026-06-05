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
use App\Models\Invoice;
use App\Enums\WorkOrderStatus;
use App\Services\Storage\StorageService;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Services\WarehouseDashboardApiService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    #[Url]
    public $search = '';

    #[Url(except: 'summary')]
    public $activeTab = 'summary';

    #[Url]
    public $activeRackTab = 'shoes';

    #[Url]
    public $dateRange = '7_days'; // options: today, 7_days, 30_days, custom

    public $ignorePiutangDateFilter = true;

    #[Url]
    public $startDate;

    #[Url]
    public $endDate;

    #[Url]
    public $sortirOverdueOnly = false;

    #[Url]
    public $productionFilter = 'all';

    public function mount()
    {
        $this->updateDateBoundaries();
    }

    public function updatedDateRange()
    {
        $this->updateDateBoundaries();
        $this->dispatchRefreshCharts();
    }

    public function updatedStartDate()
    {
        $this->dateRange = 'custom';
        $this->dispatchRefreshCharts();
    }

    public function updatedEndDate()
    {
        $this->dateRange = 'custom';
        $this->dispatchRefreshCharts();
    }

    public function setSingleDate($date)
    {
        $this->startDate = $date;
        $this->endDate = $date;
        $this->dateRange = 'custom';
        $this->dispatchRefreshCharts();
    }

    protected function updateDateBoundaries()
    {
        switch ($this->dateRange) {
            case 'today':
                $this->startDate = now()->startOfDay()->format('Y-m-d');
                $this->endDate = now()->endOfDay()->format('Y-m-d');
                break;
            case '7_days':
                $this->startDate = now()->subDays(7)->startOfDay()->format('Y-m-d');
                $this->endDate = now()->endOfDay()->format('Y-m-d');
                break;
            case '30_days':
                $this->startDate = now()->subDays(30)->startOfDay()->format('Y-m-d');
                $this->endDate = now()->endOfDay()->format('Y-m-d');
                break;
            // 'custom' doesn't change start/end automatically
        }
    }

    public function refreshData()
    {
        $this->updateDateBoundaries();
        $this->dispatchRefreshCharts();
    }

    private function dispatchRefreshCharts()
    {
        $this->dispatch('refreshCharts', [
            'qcTrends' => $this->qcTrends,
            'qcStats' => $this->qcStats,
            'efficiency' => $this->efficiencyStats,
            'heatmap' => $this->heatmapData,
            'materials' => $this->materialTrends,
            'dailyFlow' => $this->dailyFlow,
            'manifestSummary' => $this->manifestSummary
        ]);
    }

    protected function applyDateFilter($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [
            \Carbon\Carbon::parse($this->startDate)->startOfDay(),
            \Carbon\Carbon::parse($this->endDate)->endOfDay()
        ]);
    }

    public function render(StorageService $storageService, WarehouseDashboardApiService $apiService)
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        // 1. Hero Stats (Mirroring API Global Metrics)
        $stats = $apiService->getHeroMetrics($start, $end);

        // 2. Storage Metrics
        $storageMainStats = $apiService->getStorageStats();
        $storageStats = [
            ['label' => 'Total Kapasitas', 'value' => $storageMainStats['total_capacity'], 'icon' => '📦', 'color' => 'bg-blue-50 text-blue-600'],
            ['label' => 'Terpakai', 'value' => $storageMainStats['current_usage'], 'icon' => '📥', 'color' => 'bg-indigo-50 text-indigo-600'],
            ['label' => 'Slot Tersedia', 'value' => $storageMainStats['available_slots'], 'icon' => '✅', 'color' => 'bg-emerald-50 text-emerald-600'],
        ];

        // 3. Overdue & Rack Intelligence
        $overdueItems = WorkOrder::where('status', WorkOrderStatus::SELESAI)
            ->whereHas('storageAssignments', fn($q) => $q->stored()->where('stored_at', '<', now()->subDays(30)))
            ->count();

        $rackStats = [
            'total' => StorageRack::active()->count(),
            'full' => StorageRack::active()->whereColumn('current_count', '>=', 'capacity')->count(),
            'empty' => StorageRack::active()->where('current_count', 0)->count(),
        ];

        $racksByCategory = StorageRack::active()
            ->select('category', DB::raw('count(*) as count'), DB::raw('sum(current_count) as total_items'))
            ->groupBy('category')
            ->get();

        // 4. Inventory & Logs
        $lowStockMaterials = Material::where('stock', '<', 5)->get();
        $recentLogs = WorkOrderLog::with(['workOrder', 'user'])->latest()->take(10)->get();

        // 5. Operational Queues
        $queues = $apiService->getQueues($this->search);

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
            'qcTrends' => $this->qcTrends,
            'qcStats' => $this->qcStats,
            'materialTrends' => $this->materialTrends,
            'heatmapData' => $this->heatmapData,
            'efficiencyStats' => $this->efficiencyStats,
            'dailyFlow' => $this->dailyFlow,
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
            $this->dispatchRefreshCharts();
            $this->dispatch('notify', ['type' => 'success', 'message' => "Order stored to {$rackCode}"]);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function quickRetrieve($workOrderId, StorageService $storageService)
    {
        try {
            $storageService->retrieveFromStorage($workOrderId);
            $this->dispatchRefreshCharts();
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
        return app(WarehouseDashboardApiService::class)->getHeatmapData();
    }

    #[Computed]
    public function efficiencyStats()
    {
        return app(WarehouseDashboardApiService::class)->getEfficiencyStats(
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay()
        );
    }

    #[Computed]
    public function inventoryValue()
    {
        return app(WarehouseDashboardApiService::class)->getInventoryValue();
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
    public function qcTrends()
    {
        return app(WarehouseDashboardApiService::class)->getQcTrends(
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay()
        );
    }

    #[Computed]
    public function qcStats()
    {
        return app(WarehouseDashboardApiService::class)->getQcStats(
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay()
        );
    }

    #[Computed]
    public function materialTrends()
    {
        return app(WarehouseDashboardApiService::class)->getMaterialTrends();
    }

    #[Computed]
    public function dailyFlow()
    {
        return app(WarehouseDashboardApiService::class)->getDailyFlowMetrics(
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay()
        );
    }

    #[Computed]
    public function dailyDisbursementStats()
    {
        $today = now()->startOfDay();
        $end = now()->endOfDay();

        $totalQty = \App\Models\WarehouseDisbursementItem::whereHas('disbursement', function($q) use ($today, $end) {
                $q->whereBetween('disbursement_date', [$today, $end]);
            })->sum('quantity');

        $totalValue = \App\Models\WarehouseDisbursementItem::whereHas('disbursement', function($q) use ($today, $end) {
                $q->whereBetween('disbursement_date', [$today, $end]);
            })->sum('subtotal');

        return [
            'quantity' => $totalQty,
            'value' => $totalValue
        ];
    }

    #[Computed]
    public function piutangAfterOrders()
    {
        $query = Invoice::with(['customer', 'workOrders.workOrderServices.service'])
            ->where('status', '!=', 'Lunas')
            ->where('spk_status', 'SELESAI');

        if (!$this->ignorePiutangDateFilter) {
            $query = $this->applyDateFilter($query, 'created_at');
        }

        return $query->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', function ($sub) {
                          $sub->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('phone', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('workOrders', function ($sub) {
                          $sub->where('spk_number', 'like', '%' . $this->search . '%')
                              ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                              ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->latest()
            ->get();
    }

    #[Computed]
    public function totalPiutangAmount()
    {
        $query = Invoice::where('status', '!=', 'Lunas')
            ->where('spk_status', 'SELESAI');

        if (!$this->ignorePiutangDateFilter) {
            $query = $this->applyDateFilter($query, 'created_at');
        }

        return $query->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', function ($sub) {
                          $sub->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('phone', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('workOrders', function ($sub) {
                          $sub->where('spk_number', 'like', '%' . $this->search . '%')
                              ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                              ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->get()
            ->sum('remaining_balance');
    }

    #[Computed]
    public function piutangBeforeOrders()
    {
        $query = Invoice::with(['customer', 'workOrders.workOrderServices.service'])
            ->where('status', '!=', 'Lunas')
            ->whereHas('workOrders', function ($q) {
                $q->whereIn('status', [
                    \App\Enums\WorkOrderStatus::DITERIMA->value,
                    \App\Enums\WorkOrderStatus::READY_TO_DISPATCH->value,
                    \App\Enums\WorkOrderStatus::ASSESSMENT->value,
                    \App\Enums\WorkOrderStatus::WAITING_PAYMENT->value,
                    \App\Enums\WorkOrderStatus::WAITING_VERIFICATION->value,
                ]);
            });

        if (!$this->ignorePiutangDateFilter) {
            $query = $this->applyDateFilter($query, 'created_at');
        }

        return $query->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', function ($sub) {
                          $sub->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('phone', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('workOrders', function ($sub) {
                          $sub->where('spk_number', 'like', '%' . $this->search . '%')
                              ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                              ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->latest()
            ->get();
    }

    #[Computed]
    public function totalPiutangBeforeAmount()
    {
        $query = Invoice::where('status', '!=', 'Lunas')
            ->whereHas('workOrders', function ($q) {
                $q->whereIn('status', [
                    \App\Enums\WorkOrderStatus::DITERIMA->value,
                    \App\Enums\WorkOrderStatus::READY_TO_DISPATCH->value,
                    \App\Enums\WorkOrderStatus::ASSESSMENT->value,
                    \App\Enums\WorkOrderStatus::WAITING_PAYMENT->value,
                    \App\Enums\WorkOrderStatus::WAITING_VERIFICATION->value,
                ]);
            });

        if (!$this->ignorePiutangDateFilter) {
            $query = $this->applyDateFilter($query, 'created_at');
        }

        return $query->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', function ($sub) {
                          $sub->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('phone', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('workOrders', function ($sub) {
                          $sub->where('spk_number', 'like', '%' . $this->search . '%')
                              ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                              ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->get()
            ->sum('remaining_balance');
    }

    #[Computed]
    public function shoeRackOrders()
    {
        $threeMonthsAgo = now()->subMonths(3);

        $query = StorageAssignment::where('category', \App\Enums\StorageCategory::SHOES->value)
            ->stored()
            ->with(['workOrder']);

        if ($this->search) {
            $query->whereHas('workOrder', function($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('shoe_brand', 'like', '%' . $this->search . '%')
                  ->orWhere('shoe_type', 'like', '%' . $this->search . '%');
            });
        }

        return $query->latest('stored_at')->get()->map(function($assignment) use ($threeMonthsAgo) {
            $wo = $assignment->workOrder;
            $storedAt = $assignment->stored_at;
            $days = $storedAt ? (int) abs(round(now()->diffInDays($storedAt))) : 0;

            $assignment->spk_number = $wo?->spk_number ?? 'N/A';
            $assignment->customer_name = $wo?->customer_name ?? 'N/A';
            $assignment->customer_phone = $wo?->customer_phone ?? null;
            $assignment->shoe_brand = $wo?->shoe_brand ?? '-';
            $assignment->shoe_type = $wo?->shoe_type ?? '';
            $assignment->shoe_color = $wo?->shoe_color ?? null;
            $assignment->shoe_size = $wo?->shoe_size ?? null;
            $assignment->work_order_id = $wo?->id;
            $assignment->wo_status = $wo?->status?->value ?? ($wo?->status ?? '-');
            $assignment->days_stored = $days;
            $assignment->days_stored_formatted = $days === 0 ? 'Hari Ini' : $days . ' Hari';
            $assignment->is_donation_candidate = $storedAt ? $storedAt->lte($threeMonthsAgo) : false;
            $assignment->stored_at_formatted = $storedAt ? $storedAt->format('d M Y H:i') : '-';
            return $assignment;
        });
    }

    #[Computed]
    public function manifestSummary()
    {
        return app(WarehouseDashboardApiService::class)->getManifestSummary(
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay(),
            $this->search
        );
    }

    #[Computed]
    public function sortirSummary()
    {
        return app(WarehouseDashboardApiService::class)->getSortirSummary(
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay(),
            $this->search,
            $this->sortirOverdueOnly
        );
    }

    #[Computed]
    public function productionSummary()
    {
        return app(WarehouseDashboardApiService::class)->getProductionSummary(
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay(),
            $this->search,
            $this->productionFilter
        );
    }

    public function moveToDonation($workOrderId, StorageService $storageService)
    {
        try {
            DB::transaction(function() use ($workOrderId, $storageService) {
                $workOrder = WorkOrder::findOrFail($workOrderId);
                
                // 1. Retrieve from storage to free slot and decrement rack count
                if ($workOrder->storage_rack_code) {
                    $storageService->retrieveFromStorage($workOrderId, 'Dipindahkan ke Donasi (Mengendap > 3 Bulan)');
                }
                
                // 2. Update status and donated_at
                $workOrder->update([
                    'status' => WorkOrderStatus::DONASI->value,
                    'donated_at' => now(),
                    'notes' => $workOrder->notes . "\n[SISTEM] Dipindahkan ke Donasi oleh " . auth()->user()->name . " karena mengendap > 3 bulan di rak."
                ]);
            });

            $this->dispatchRefreshCharts();
            $this->dispatch('notify', ['type' => 'success', 'message' => "Order dipindahkan ke Donasi dan rak dibebaskan."]);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
