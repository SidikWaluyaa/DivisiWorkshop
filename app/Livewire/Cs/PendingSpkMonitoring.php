<?php

namespace App\Livewire\Cs;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use App\Helpers\ActivityLogger;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendingSpkMonitoring extends Component
{
    use WithPagination;

    #[Url(except: 'all')]
    public $activeTab = 'all'; // all | in_transit | waiting

    #[Url(except: '')]
    public $search = '';

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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
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

        // Tab Filter
        if ($this->activeTab === 'in_transit') {
            $query->whereNotNull('customer_tracking_number');
        } elseif ($this->activeTab === 'waiting') {
            $query->whereNull('customer_tracking_number');
        }

        // Search Filter
        if (!empty($this->search)) {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('spk_number', 'like', "%{$term}%")
                  ->orWhere('customer_name', 'like', "%{$term}%")
                  ->orWhere('customer_phone', 'like', "%{$term}%")
                  ->orWhere('shoe_brand', 'like', "%{$term}%")
                  ->orWhere('customer_tracking_number', 'like', "%{$term}%");
            });
        }

        $orders = $query->latest('id')->paginate($this->perPage);

        return view('livewire.cs.pending-spk-monitoring', [
            'orders' => $orders,
            'counts' => $this->counts,
        ])->layout('layouts.app');
    }
}
