<?php

namespace App\Livewire\Warranty;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\WorkOrder;
use App\Models\WorkOrderWarranty;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $searchSpk = ''; // For modal
    public $step = 1; // 1: Search WO, 2: Fill Warranty
    public $selectedWorkOrderId = null;
    public $description = '';
    public $photos = [];
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
        $this->reset(['searchSpk', 'step', 'selectedWorkOrderId', 'description', 'photos']);
        $this->showCreateModal = true;
    }

    public function selectWorkOrder($id)
    {
        $this->selectedWorkOrderId = $id;
        $this->step = 2;
    }

    public function removePhoto($index)
    {
        if (is_array($this->photos)) {
            array_splice($this->photos, $index, 1);
        } else {
            $this->photos = [];
        }
    }

    public function saveWarranty()
    {
        $this->validate([
            'selectedWorkOrderId' => 'required',
            'description' => 'required|string|max:1000',
            'photos.*' => 'nullable|image|max:5120', // Max 5MB per photo
        ]);

        $wo = WorkOrder::find($this->selectedWorkOrderId);
        
        // Handle Photo Uploads with compression
        $photoPaths = [];
        if (!empty($this->photos)) {
            // Ensure $this->photos is iterable (handle single vs multiple)
            $photoArray = is_array($this->photos) ? $this->photos : [$this->photos];
            
            foreach ($photoArray as $photo) {
                if ($photo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    $filename = 'WARRANTY_' . $wo->spk_number . '_' . time() . '_' . rand(100, 999);
                    $path = \App\Utils\ImageHelper::convertToJpg($photo, 'warranty-issues', $filename);
                    $photoPaths[] = $path;
                }
            }
        }
        
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

        // Create NEW WorkOrder for this warranty rework
        $reworkWo = WorkOrder::create([
            'spk_number' => $garansiSpk,
            'customer_name' => $wo->customer_name,
            'customer_phone' => $wo->customer_phone,
            'customer_email' => $wo->customer_email,
            'customer_address' => $wo->customer_address,
            'shoe_brand' => $wo->shoe_brand,
            'shoe_type' => $wo->shoe_type,
            'shoe_color' => $wo->shoe_color,
            'shoe_size' => $wo->shoe_size,
            'category' => $wo->category,
            'category_spk' => $wo->category_spk,
            'status' => \App\Enums\WorkOrderStatus::SELESAI->value,
            'is_warranty' => true,
            'parent_id' => $wo->id,
            'notes' => 'GARANSI DARI SPK: ' . $wo->spk_number . '. Keluhan: ' . $this->description,
            'total_transaksi' => 0,
            'status_pembayaran' => 'L', // Assume Lunas for warranty
            'created_by' => Auth::id(),
            'entry_date' => now(),
        ]);

        WorkOrderWarranty::create([
            'work_order_id' => $wo->id,
            'garansi_spk_number' => $garansiSpk,
            'description' => $this->description,
            'photos' => $photoPaths,
            'status' => 'OPEN',
            'created_by' => Auth::id()
        ]);

        $this->showCreateModal = false;
        $this->reset(['searchSpk', 'step', 'selectedWorkOrderId', 'description', 'photos']);
        session()->flash('success', 'Laporan garansi berhasil dibuat! Nomor SPK-nya: ' . $garansiSpk . '. Silahkan upload foto hasil perbaikan di stasiun FINISH.');
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
            
            // Auto-sync to Finish Station if not already there
            $this->syncToFinish($id);

            session()->flash('success', 'Perbaikan garansi untuk SPK '.$warranty->garansi_spk_number.' sudah tuntas dikerjakan!');
        }
    }

    public function syncToFinish($id)
    {
        $warranty = WorkOrderWarranty::find($id);
        if (!$warranty) return;

        $wo = $warranty->workOrder; // Original WO

        // Check if rework WO already exists
        $reworkWo = WorkOrder::where('spk_number', $warranty->garansi_spk_number)->first();

        if (!$reworkWo) {
            // Create NEW WorkOrder for this warranty rework
            $reworkWo = WorkOrder::create([
                'spk_number' => $warranty->garansi_spk_number,
                'customer_name' => $wo->customer_name,
                'customer_phone' => $wo->customer_phone,
                'customer_email' => $wo->customer_email,
                'customer_address' => $wo->customer_address,
                'shoe_brand' => $wo->shoe_brand,
                'shoe_type' => $wo->shoe_type,
                'shoe_color' => $wo->shoe_color,
                'shoe_size' => $wo->shoe_size,
                'category' => $wo->category,
                'category_spk' => $wo->category_spk,
                'status' => \App\Enums\WorkOrderStatus::SELESAI->value,
                'is_warranty' => true,
                'parent_id' => $wo->id,
                'notes' => 'GARANSI DARI SPK: ' . $wo->spk_number . '. Keluhan: ' . $warranty->description,
                'total_transaksi' => 0,
                'status_pembayaran' => 'L',
                'created_by' => $warranty->created_by,
                'entry_date' => $warranty->created_at,
            ]);
        }

        return redirect()->route('finish.show', $reworkWo->id);
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
            'finisher',
            'reworkWorkOrder',
            'workOrder.customer'
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
