<?php

namespace App\Livewire\Cs;

use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use App\Helpers\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PendingSpkMonitoring extends Component
{
    use WithPagination;

    #[Url(except: 'all')]
    public $activeTab = 'all'; // all | in_transit | waiting

    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $dateRange = '';

    #[Url(except: 'created_at')]
    public $dateField = 'created_at'; // created_at | customer_shipped_at

    #[Url(except: '')]
    public $brandFilter = '';

    #[Url(except: '')]
    public $channelFilter = '';

    #[Url(except: '')]
    public $creatorFilter = '';

    #[Url(except: 'latest')]
    public $sortBy = 'latest'; // latest | oldest | customer_asc | customer_desc | resi_latest

    public $perPage = 15;

    // Modal Properties
    public $showModal = false;
    public $selectedWorkOrderId = null;
    public $trackingNumber = '';
    public $selectedWorkOrder = null;

    protected $rules = [
        'trackingNumber' => 'required|string|min:3|max:100',
    ];

    protected $messages = [
        'trackingNumber.required' => 'Nomor resi wajib diisi.',
        'trackingNumber.min' => 'Nomor resi minimal 3 karakter.',
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingActiveTab() { $this->resetPage(); }
    public function updatingDateRange() { $this->resetPage(); }
    public function updatingDateField() { $this->resetPage(); }
    public function updatingBrandFilter() { $this->resetPage(); }
    public function updatingChannelFilter() { $this->resetPage(); }
    public function updatingCreatorFilter() { $this->resetPage(); }
    public function updatingSortBy() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function setPresetDate($preset)
    {
        $today = now()->format('Y-m-d');
        if ($preset === 'today') {
            $this->dateRange = "{$today} to {$today}";
        } elseif ($preset === '7days') {
            $startDate = now()->subDays(6)->format('Y-m-d');
            $this->dateRange = "{$startDate} to {$today}";
        } elseif ($preset === 'this_month') {
            $startDate = now()->startOfMonth()->format('Y-m-d');
            $endDate = now()->endOfMonth()->format('Y-m-d');
            $this->dateRange = "{$startDate} to {$endDate}";
        } elseif ($preset === 'clear') {
            $this->dateRange = '';
        }
        $this->resetPage();
    }

    public function resetAllFilters()
    {
        $this->search = '';
        $this->dateRange = '';
        $this->dateField = 'created_at';
        $this->brandFilter = '';
        $this->channelFilter = '';
        $this->creatorFilter = '';
        $this->sortBy = 'latest';
        $this->activeTab = 'all';
        $this->resetPage();
    }

    #[Computed]
    public function isFiltered()
    {
        return !empty($this->search) || 
               !empty($this->dateRange) || 
               !empty($this->brandFilter) || 
               !empty($this->channelFilter) || 
               !empty($this->creatorFilter) || 
               $this->activeTab !== 'all' || 
               $this->sortBy !== 'latest';
    }

    #[Computed]
    public function counts()
    {
        return [
            'all' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->count(),
            'in_transit' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->whereNotNull('customer_tracking_number')->count(),
            'waiting' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->whereNull('customer_tracking_number')->count(),
        ];
    }

    #[Computed]
    public function availableBrands()
    {
        return WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)
            ->whereNotNull('shoe_brand')
            ->where('shoe_brand', '!=', '')
            ->distinct()
            ->orderBy('shoe_brand')
            ->pluck('shoe_brand');
    }

    #[Computed]
    public function availableChannels()
    {
        return WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)
            ->whereNotNull('channel')
            ->where('channel', '!=', '')
            ->distinct()
            ->orderBy('channel')
            ->pluck('channel');
    }

    #[Computed]
    public function availableCreators()
    {
        $creatorIds = WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)
            ->whereNotNull('created_by')
            ->distinct()
            ->pluck('created_by');

        return User::whereIn('id', $creatorIds)->orderBy('name')->get(['id', 'name']);
    }

    public function openResiModal($woId)
    {
        $wo = WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->findOrFail($woId);
        $this->selectedWorkOrderId = $wo->id;
        $this->selectedWorkOrder = $wo;
        $this->trackingNumber = $wo->customer_tracking_number ?? '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedWorkOrderId = null;
        $this->selectedWorkOrder = null;
        $this->trackingNumber = '';
        $this->resetValidation();
    }

    public function saveResi()
    {
        $this->validate();

        $wo = WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->findOrFail($this->selectedWorkOrderId);
        $user = Auth::user();

        $cleanResi = strtoupper(trim($this->trackingNumber));

        DB::transaction(function () use ($wo, $cleanResi, $user) {
            $wo->update([
                'customer_tracking_number' => $cleanResi,
                'customer_shipped_at' => now(),
            ]);

            $wo->logs()->create([
                'user_id' => $user?->id,
                'step' => 'CS_PENDING',
                'action' => 'RESI_INPUTTED',
                'description' => "Nomor Resi '{$cleanResi}' diinput oleh CS ({$user?->name}). Sepatu sedang dalam perjalanan menuju workshop.",
            ]);

            ActivityLogger::log('Input Resi Customer', "CS ({$user?->name}) menginput resi '{$cleanResi}' untuk SPK {$wo->spk_number}.");
        });

        $this->closeModal();

        $this->dispatch('swal:toast', [
            'icon' => 'success',
            'title' => "Resi SPK #{$wo->spk_number} berhasil disimpan!",
        ]);
    }

    public function clearResi($woId)
    {
        $wo = WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->findOrFail($woId);
        $user = Auth::user();

        DB::transaction(function () use ($wo, $user) {
            $oldResi = $wo->customer_tracking_number;
            $wo->update([
                'customer_tracking_number' => null,
                'customer_shipped_at' => null,
            ]);

            $wo->logs()->create([
                'user_id' => $user?->id,
                'step' => 'CS_PENDING',
                'action' => 'RESI_CLEARED',
                'description' => "Nomor Resi '{$oldResi}' dihapus oleh CS ({$user?->name}). Status kembali menunggu kiriman.",
            ]);

            ActivityLogger::log('Hapus Resi Customer', "CS ({$user?->name}) menghapus resi '{$oldResi}' pada SPK {$wo->spk_number}.");
        });

        $this->dispatch('swal:toast', [
            'icon' => 'info',
            'title' => "Resi SPK #{$wo->spk_number} berhasil dikosongkan.",
        ]);
    }

    public function render()
    {
        $query = WorkOrder::query()
            ->with(['services.service', 'creator'])
            ->where('status', WorkOrderStatus::SPK_PENDING);

        // 1. Tab Filter
        if ($this->activeTab === 'in_transit') {
            $query->whereNotNull('customer_tracking_number');
        } elseif ($this->activeTab === 'waiting') {
            $query->whereNull('customer_tracking_number');
        }

        // 2. Search Filter
        if (!empty($this->search)) {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('spk_number', 'like', "%{$term}%")
                  ->orWhere('customer_name', 'like', "%{$term}%")
                  ->orWhere('customer_phone', 'like', "%{$term}%")
                  ->orWhere('shoe_brand', 'like', "%{$term}%")
                  ->orWhere('shoe_type', 'like', "%{$term}%")
                  ->orWhere('customer_tracking_number', 'like', "%{$term}%");
            });
        }

        // 3. Date Range Filter
        if (!empty($this->dateRange)) {
            $dates = explode(' to ', $this->dateRange);
            $field = in_array($this->dateField, ['created_at', 'customer_shipped_at']) ? $this->dateField : 'created_at';

            if (count($dates) === 2) {
                $start = Carbon::parse($dates[0])->startOfDay();
                $end = Carbon::parse($dates[1])->endOfDay();
                $query->whereBetween($field, [$start, $end]);
            } elseif (count($dates) === 1 && !empty($dates[0])) {
                $start = Carbon::parse($dates[0])->startOfDay();
                $end = Carbon::parse($dates[0])->endOfDay();
                $query->whereBetween($field, [$start, $end]);
            }
        }

        // 4. Dropdown Filters
        if (!empty($this->brandFilter)) {
            $query->where('shoe_brand', $this->brandFilter);
        }

        if (!empty($this->channelFilter)) {
            $query->where('channel', $this->channelFilter);
        }

        if (!empty($this->creatorFilter)) {
            $query->where('created_by', $this->creatorFilter);
        }

        // 5. Sorting
        switch ($this->sortBy) {
            case 'oldest':
                $query->oldest('id');
                break;
            case 'customer_asc':
                $query->orderBy('customer_name', 'asc');
                break;
            case 'customer_desc':
                $query->orderBy('customer_name', 'desc');
                break;
            case 'resi_latest':
                $query->orderByRaw('CASE WHEN customer_shipped_at IS NOT NULL THEN 0 ELSE 1 END')
                      ->latest('customer_shipped_at')
                      ->latest('id');
                break;
            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        $orders = $query->paginate($this->perPage);

        return view('livewire.cs.pending-spk-monitoring', [
            'orders' => $orders,
            'counts' => $this->counts,
            'brands' => $this->availableBrands,
            'channels' => $this->availableChannels,
            'creators' => $this->availableCreators,
        ])->layout('layouts.app');
    }
}
