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
        
        $materialRequest = MaterialRequest::with(['items.material', 'items.workOrder', 'workOrder'])->find($id);
        
        if (!$materialRequest || $materialRequest->status === 'RECEIVED' || $materialRequest->status === 'CANCELLED' || $materialRequest->status === 'REJECTED') {
            $this->dispatch('notify', type: 'error', message: 'Request tidak valid atau sudah selesai diterima.');
            return;
        }

        $service = app(\App\Services\MaterialManagementService::class);
        $workflow = app(\App\Services\WorkflowService::class);

        \Illuminate\Support\Facades\DB::transaction(function () use ($materialRequest, $service, $workflow) {
            $materialRequest->refresh();
            if ($materialRequest->status === 'RECEIVED') return;

            // 1. Mark status as RECEIVED (Bahan Baku Tiba di Workshop)
            $materialRequest->markAsReceived();

            // 2. Increment Stock & Log Transaction
            foreach ($materialRequest->items as $item) {
                if ($item->material) {
                    $service->restock(
                        $item->material,
                        $item->quantity,
                        "Penerimaan & verifikasi fisik barang dari Pengajuan #{$materialRequest->request_number}",
                        'MaterialRequest',
                        $materialRequest->id
                    );
                }
            }

            // 3. Global Auto-Allocation
            $service->autoAllocateStock();

            // 4. Update linked WorkOrders to set arrival date & advance status to Production
            $workOrders = collect();
            if ($materialRequest->work_order_id && $materialRequest->workOrder) {
                $workOrders->push($materialRequest->workOrder);
            }
            foreach ($materialRequest->items as $item) {
                if ($item->workOrder) {
                    $workOrders->push($item->workOrder);
                }
            }

            foreach ($workOrders->unique('id') as $order) {
                $order->material_arrival_date = now();
                $order->save();

                try {
                    $workflow->updateStatus(
                        $order,
                        \App\Enums\WorkOrderStatus::PRODUCTION,
                        "Material pengajuan (#{$materialRequest->request_number}) diverifikasi & diterima fisik oleh " . \Illuminate\Support\Facades\Auth::user()->name . ". SPK dilanjutkan ke Stasiun Produksi."
                    );
                } catch (\Throwable $e) {
                    $order->status = \App\Enums\WorkOrderStatus::PRODUCTION;
                    $order->save();

                    $order->logs()->create([
                        'user_id' => \Illuminate\Support\Facades\Auth::id(),
                        'step' => 'SORTIR_BELANJA',
                        'action' => 'RECEIVE_MATERIAL',
                        'description' => "Material pengajuan (#{$materialRequest->request_number}) diterima fisik.",
                    ]);
                }
            }
        });

        $this->dispatch('notify', type: 'success', message: "Material pengajuan #{$materialRequest->request_number} berhasil diterima fisik & SPK otomatis pindah ke Stasiun Produksi!");
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
        ])->layout('layouts.workshop-pwa');
    }
}
