<?php

namespace App\Livewire\Warranty;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use App\Models\WorkOrderWarranty;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $searchSpk = ''; // For modal
    public $step = 1; // 1: Search WO, 2: Fill Warranty
    public $selectedWorkOrderId = null;
    public $description = '';
    public $showCreateModal = false;
    public $activeTab = 'active'; // 'active' or 'history'

    // Real-time Filters
    public $dateFrom = '';
    public $dateTo = '';
    public $filterPic = '';
    public $filterCategory = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'active'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'filterPic' => ['except' => ''],
        'filterCategory' => ['except' => ''],
    ];

    public function resetFilters()
    {
        $this->reset(['dateFrom', 'dateTo', 'filterPic', 'filterCategory', 'search']);
        $this->resetPage();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updated($property)
    {
        if (in_array($property, ['dateFrom', 'dateTo', 'filterPic', 'filterCategory', 'search'])) {
            $this->resetPage();
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function openCreateModal()
    {
        $this->reset(['searchSpk', 'step', 'selectedWorkOrderId', 'description']);
        $this->showCreateModal = true;
    }

    public function selectWorkOrder($id)
    {
        $this->selectedWorkOrderId = $id;
        $this->step = 2;
    }

    public function saveWarranty()
    {
        $this->validate([
            'selectedWorkOrderId' => 'required',
            'description' => 'required|string|max:1000',
        ]);

        $wo = WorkOrder::find($this->selectedWorkOrderId);
        
        // Generate GR SPK (e.g., replace last segment with GR)
        // Original logic from CsSpkService: explode by '-' and replace last part
        $parts = explode('-', $wo->spk_number);
        if (count($parts) > 1) {
            array_pop($parts);
            $parts[] = 'GR';
            $garansiSpk = implode('-', $parts);
        } else {
            $garansiSpk = $wo->spk_number . '-GR';
        }

        // Add increment if exist
        $baseSpk = $garansiSpk;
        $counter = 1;
        while (WorkOrderWarranty::where('garansi_spk_number', $garansiSpk)->exists()) {
            $garansiSpk = $baseSpk . '-' . $counter;
            $counter++;
        }

        WorkOrderWarranty::create([
            'work_order_id' => $wo->id,
            'garansi_spk_number' => $garansiSpk,
            'description' => $this->description,
            'status' => 'OPEN',
            'created_by' => Auth::id()
        ]);

        $this->showCreateModal = false;
        $this->reset(['searchSpk', 'step', 'selectedWorkOrderId', 'description']);
        session()->flash('success', 'Laporan garansi berhasil dibuat! Nomor SPK-nya: ' . $garansiSpk);
    }
    
    public function finishWarranty($id)
    {
        $warranty = WorkOrderWarranty::find($id);
        if ($warranty && $warranty->status === 'OPEN') {
            $warranty->update([
                'status' => 'FINISHED',
                'finished_by' => Auth::id(),
                'finished_at' => now(),
            ]);
            session()->flash('success', 'Perbaikan garansi untuk SPK '.$warranty->garansi_spk_number.' sudah tuntas dikerjakan!');
        }
    }

    public function getStatsProperty()
    {
        $baseQuery = \App\Models\WorkOrderWarranty::query();
        $this->applyFilters($baseQuery);

        $total = (clone $baseQuery)->count();
        $open = (clone $baseQuery)->where('status', 'OPEN')->count();
        $finished = (clone $baseQuery)->where('status', 'FINISHED')->count();
        
        $topCategory = (clone $baseQuery)->join('work_orders', 'work_order_warranties.work_order_id', '=', 'work_orders.id')
            ->select('work_orders.category_spk', \DB::raw('count(*) as count'))
            ->groupBy('work_orders.category_spk')
            ->orderByDesc('count')
            ->first();

        return [
            'total' => $total,
            'open' => $open,
            'finished' => $finished,
            'topCategory' => $topCategory->category_spk ?? '-',
        ];
    }

    private function applyFilters($query)
    {
        if ($this->dateFrom) {
            $query->whereDate('work_order_warranties.created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('work_order_warranties.created_at', '<=', $this->dateTo);
        }
        if ($this->filterPic) {
            $query->where('work_order_warranties.created_by', $this->filterPic);
        }
        if ($this->filterCategory) {
            $query->whereHas('workOrder', function ($q) {
                $q->where('category_spk', $this->filterCategory);
            });
        }
        if ($this->search) {
            $query->whereHas('workOrder', function ($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%');
            });
        }
    }

    public function getSelectedWorkOrderProperty()
    {
        if ($this->selectedWorkOrderId) {
            return \App\Models\WorkOrder::with(['workOrderServices.service', 'photos'])->find($this->selectedWorkOrderId);
        }
        return null;
    }

    public function render()
    {
        $query = \App\Models\WorkOrderWarranty::with([
            'workOrder.workOrderServices.technician',
            'workOrder.prepWashingBy',
            'workOrder.prepSolBy',
            'workOrder.prepUpperBy',
            'workOrder.prodSolBy',
            'workOrder.prodUpperBy',
            'workOrder.prodCleaningBy',
            'workOrder.qcJahitBy',
            'workOrder.qcCleanupBy',
            'workOrder.qcFinalBy',
            'workOrder.technicianProduction',
            'workOrder.picSortirSol',
            'workOrder.picSortirUpper',
            'creator', 
            'finisher'
        ])->latest();

        $this->applyFilters($query);

        if ($this->activeTab === 'active') {
            $query->where('status', 'OPEN');
        } else {
            $query->where('status', 'FINISHED');
        }

        // Get available filters data
        $availablePics = \App\Models\User::whereHas('warrantiesCreated')->get();
        $availableCategories = \App\Models\WorkOrder::whereNotNull('category_spk')
            ->where('category_spk', '!=', '')
            ->where('category_spk', '!=', '-')
            ->distinct()
            ->pluck('category_spk');

        // Get WO for Modal Search (Status SELESAI & taken_date NOT NULL)
        $searchSource = [];
        if ($this->step === 1 && $this->showCreateModal) {
            $modalQuery = \App\Models\WorkOrder::with('workOrderServices', 'photos', 'warranties')
                ->where('status', \App\Enums\WorkOrderStatus::SELESAI->value)
                ->whereNotNull('taken_date');

            if ($this->searchSpk) {
                $modalQuery->where(function($q) {
                    $q->where('spk_number', 'like', '%' . $this->searchSpk . '%')
                      ->orWhere('customer_name', 'like', '%' . $this->searchSpk . '%');
                });
            }

            $searchSource = $modalQuery->latest()->take(10)->get();
        }

        return view('livewire.warranty.index', [
            'warranties' => $query->paginate(10),
            'searchSource' => $searchSource,
            'availablePics' => $availablePics,
            'availableCategories' => $availableCategories,
            'stats' => $this->stats,
        ])->layout('layouts.app');
    }
}
