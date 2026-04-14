<?php

namespace App\Livewire\Procurement;

use App\Models\MaterialRequest;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status = 'all';
    public $type = 'all';
    public $dateRange = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
        'type' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function quickFulfill($id)
    {
        Gate::authorize('manageInventory', \App\Models\WorkOrder::class);
        
        $materialRequest = MaterialRequest::with('items.material')->find($id);
        
        if (!$materialRequest || (!$materialRequest->isApproved() && !$materialRequest->isPending())) {
            $this->dispatch('notify', type: 'error', message: 'Request tidak valid atau sudah diproses.');
            return;
        }

        $service = app(\App\Services\MaterialManagementService::class);

        \Illuminate\Support\Facades\DB::transaction(function () use ($materialRequest, $service) {
            // REFRESH & LOCK for concurrency safety
            $materialRequest->refresh();
            if ($materialRequest->status === 'PURCHASED') return;

            // 1. Mark status as PURCHASED
            $materialRequest->markAsPurchased();

            // 2. Increment Stock & Log Transaction
            foreach ($materialRequest->items as $item) {
                if ($item->material) {
                    $service->restock(
                        $item->material,
                        $item->quantity,
                        "Penerimaan barang dari Pengajuan #{$materialRequest->request_number} (Quick Fulfill)",
                        'MaterialRequest',
                        $materialRequest->id
                    );
                }
            }

            // 3. Global Auto-Allocation
            $service->autoAllocateStock();
        });

        $this->dispatch('notify', type: 'success', message: 'Barang berhasil diterima & SPK otomatis pindah ke Siap Produksi.');
    }

    public function deleteRequest($id)
    {
        Gate::authorize('manageInventory', \App\Models\WorkOrder::class);
        
        $request = MaterialRequest::find($id);
        if ($request) {
            // Delete items first if they don't cascade (usually better to be explicit)
            $request->items()->delete();
            $request->delete();
            
            $this->dispatch('notify', type: 'success', message: 'Pengajuan material berhasil dihapus.');
        }
    }

    public function render()
    {
        $query = MaterialRequest::with(['requestedBy', 'approvedBy', 'items.material', 'workOrder', 'oto'])
            ->latest();

        if ($this->type !== 'all') {
            $query->where('type', $this->type);
        }

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhereHas('requestedBy', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply Date Range logic if implemented
        // For now we keep it simple as per Controller
        
        $requests = $query->paginate(10);

        return view('livewire.procurement.index', [
            'requests' => $requests
        ])->layout('layouts.app');
    }
}
