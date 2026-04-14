<?php

namespace App\Livewire\Sortir;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use App\Services\MaterialManagementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'ready';
    public $selectedItems = [];

    // Filter & Sort Properties
    public $sortBy = 'priority_newest';
    public $filterPriority = '';
    public $filterBrand = '';
    public $filterType = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'ready'],
        'sortBy' => ['except' => 'priority_newest'],
        'filterPriority' => ['except' => ''],
        'filterBrand' => ['except' => ''],
        'filterType' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage('readyPage');
        $this->resetPage('waitingPage');
        $this->resetPage('needsPage');
    }

    public function resetFilters()
    {
        $this->reset(['filterPriority', 'filterBrand', 'filterType', 'search']);
        $this->sortBy = 'priority_newest';
    }

    public function toggleGroup($ids)
    {
        $ids = array_map('strval', $ids);
        $allSelected = collect($ids)->every(fn($id) => in_array($id, $this->selectedItems));

        if ($allSelected) {
            $this->selectedItems = array_diff($this->selectedItems, $ids);
        } else {
            foreach ($ids as $id) {
                if (!in_array($id, $this->selectedItems)) {
                    $this->selectedItems[] = $id;
                }
            }
        }
    }

    public function bulkSkipToProduction()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih order terlebih dahulu.']);
            return;
        }

        if (!in_array(Auth::user()->role, ['admin', 'owner', 'production_manager'])) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Hanya Admin/Manager yang bisa bypass.']);
            return;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($this->selectedItems as $id) {
            try {
                $order = WorkOrder::findOrFail($id);
                $oldStatus = $order->status;

                DB::transaction(function () use ($order, $oldStatus) {
                    $order->status = WorkOrderStatus::PRODUCTION;
                    $order->current_location = 'Rumah Abu';
                    $order->save();

                    \App\Events\WorkOrderStatusUpdated::dispatch(
                        $order, 
                        $oldStatus, 
                        WorkOrderStatus::PRODUCTION, 
                        'Bulk Bypass (Livewire)', 
                        Auth::id()
                    );
                });
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
            }
        }

        $this->selectedItems = [];
        $this->dispatch('notify', ['type' => 'success', 'message' => "Bulk Bypass selesai. Berhasil: $successCount, Gagal: $failCount"]);
    }

    public function bypassSingle($id)
    {
        if (!in_array(Auth::user()->role, ['admin', 'owner', 'production_manager'])) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Hanya Admin/Manager yang bisa bypass.']);
            return;
        }

        try {
            $order = WorkOrder::findOrFail($id);
            $oldStatus = $order->status;

            DB::transaction(function () use ($order, $oldStatus) {
                $order->status = WorkOrderStatus::PRODUCTION;
                $order->current_location = 'Rumah Abu';
                $order->save();

                \App\Events\WorkOrderStatusUpdated::dispatch(
                    $order, 
                    $oldStatus, 
                    WorkOrderStatus::PRODUCTION, 
                    'Single Bypass (Sortir Index)', 
                    Auth::id()
                );
            });

            $this->dispatch('notify', ['type' => 'success', 'message' => "Order #{$order->spk_number} berhasil dikirim ke Production."]);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal bypass: ' . $e->getMessage()]);
        }
    }

    public function requestMaterial(MaterialManagementService $service, $id)
    {
        try {
            $order = WorkOrder::findOrFail($id);
            $request = $service->requestMissingMaterialsForWorkOrder($order);

            if ($request) {
                $this->dispatch('notify', [
                    'type' => 'success', 
                    'message' => "Request #{$request->request_number} berhasil dibuat & dikirim ke Purchasing."
                ]);
            } else {
                $this->dispatch('notify', ['type' => 'info', 'message' => "Tidak ada material yang perlu direquest."]);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => "Gagal: " . $e->getMessage()]);
        }
    }

    protected function applyFilters($query)
    {
        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterPriority) {
            if ($this->filterPriority === 'Prioritas') {
                $query->whereIn('priority', ['Prioritas', 'Urgent', 'Express']);
            } elseif ($this->filterPriority === 'Reguler') {
                $query->whereIn('priority', ['Reguler', 'Normal']);
            } else {
                $query->where('priority', $this->filterPriority);
            }
        }

        if ($this->filterBrand) {
            $query->where('shoe_brand', $this->filterBrand);
        }

        if ($this->filterType) {
            $query->where('shoe_type', $this->filterType);
        }

        // Sorting Logic
        switch ($this->sortBy) {
            case 'newest_spk':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest_spk':
                $query->orderBy('created_at', 'asc');
                break;
            case 'spk_asc':
                $query->orderBy('spk_number', 'asc');
                break;
            case 'priority_newest':
            default:
                $query->orderByRaw("CASE 
                    WHEN priority IN ('Prioritas', 'Urgent', 'Express') THEN 1 
                    ELSE 2 
                END ASC, created_at DESC");
                break;
        }

        return $query;
    }

    public function render(MaterialManagementService $materialService)
    {
        // 0. Proactive Auto-Allocation (ensure available stock is allocated)
        $materialService->autoAllocateStock();

        // Fetch Lists for Filters
        $baseQuery = WorkOrder::where('status', WorkOrderStatus::SORTIR->value);
        
        $availableBrands = (clone $baseQuery)
            ->distinct()
            ->whereNotNull('shoe_brand')
            ->orderBy('shoe_brand')
            ->pluck('shoe_brand');

        $availableTypes = (clone $baseQuery)
            ->distinct()
            ->whereNotNull('shoe_type')
            ->orderBy('shoe_type')
            ->pluck('shoe_type');

        // 1. Ready Queue
        $readyQuery = WorkOrder::readyForProduction()
            ->with(['customer', 'services', 'materials', 'cxIssues']);
        $readyOrders = $this->applyFilters($readyQuery)->paginate(20, ['*'], 'readyPage');

        // 2. Waiting Queue (With active MaterialRequest)
        $waitingQuery = WorkOrder::waitingForMaterials()
            ->with(['customer', 'services', 'materials', 'cxIssues']);
        $waitingOrders = $this->applyFilters($waitingQuery)->paginate(20, ['*'], 'waitingPage');

        // 3. Needs Request Queue (Missing material BUT No active MaterialRequest)
        $needsRequestQuery = WorkOrder::needsMaterialRequest()
            ->with(['customer', 'services', 'materials', 'cxIssues']);
        $needsRequestOrders = $this->applyFilters($needsRequestQuery)->paginate(20, ['*'], 'needsPage');

        // Metric calculations (counts ignore filters for consistency in headers)
        $readyCount = WorkOrder::readyForProduction()->count();
        $waitingCount = WorkOrder::waitingForMaterials()->count();
        $needsRequestCount = WorkOrder::needsMaterialRequest()->count();
        $totalCount = $readyCount + $waitingCount + $needsRequestCount;

        return view('livewire.sortir.index', [
            'readyOrders' => $readyOrders,
            'waitingOrders' => $waitingOrders,
            'needsRequestOrders' => $needsRequestOrders,
            'readyCount' => $readyCount,
            'waitingCount' => $waitingCount,
            'needsRequestCount' => $needsRequestCount,
            'totalCount' => $totalCount,
            'availableBrands' => $availableBrands,
            'availableTypes' => $availableTypes,
        ])->layout('layouts.app');
    }
}
