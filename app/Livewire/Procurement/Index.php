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

            // 3. Update linked WorkOrders to isolate materials (set pivot status to RECEIVED)
            $workOrders = collect();
            if ($materialRequest->work_order_id && $materialRequest->workOrder) {
                $workOrders->push($materialRequest->workOrder);
            }
            foreach ($materialRequest->items as $item) {
                if ($item->workOrder) {
                    $workOrders->push($item->workOrder);
                }
                if ($item->work_order_id && $item->material_id) {
                    \Illuminate\Support\Facades\DB::table('work_order_materials')
                        ->where('work_order_id', $item->work_order_id)
                        ->where('material_id', $item->material_id)
                        ->update(['status' => 'RECEIVED']);
                }
            }

            if ($materialRequest->work_order_id) {
                foreach ($materialRequest->items as $item) {
                    if ($item->material_id) {
                        \Illuminate\Support\Facades\DB::table('work_order_materials')
                            ->where('work_order_id', $materialRequest->work_order_id)
                            ->where('material_id', $item->material_id)
                            ->update(['status' => 'RECEIVED']);
                    }
                }
            }

            // 4. Update linked WorkOrders status, location, arrival date, and perlu_belanja flag
            foreach ($workOrders->unique('id') as $order) {
                $hasUnfulfilled = $order->materials()
                    ->wherePivot('status', 'REQUESTED')
                    ->exists();

                $order->material_arrival_date = now();
                if (!$hasUnfulfilled) {
                    $order->perlu_belanja = false;
                    $order->current_location = 'Sortir (Siap Handover)';
                }
                $order->save();

                // Log material receipt & allocation to WorkOrder
                $order->logs()->create([
                    'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                    'step' => 'SORTIR',
                    'action' => 'CLASSIFICATION_COMPLETED',
                    'description' => "Material pengajuan (#{$materialRequest->request_number}) diverifikasi & diterima fisik. Bahan baku terisolir ke SPK #{$order->spk_number}. SPK Siap Surat Jalan (Sortir ➔ Produksi).",
                ]);
            }

            // 5. Global Auto-Allocation for other non-shopping orders
            $service->autoAllocateStock();
        });

        $this->dispatch('notify', type: 'success', message: "Material pengajuan #{$materialRequest->request_number} berhasil diterima fisik & SPK siap diserah-terimakan via Surat Jalan!");
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

        // Compute Summary Metrics (1 single aggregate query)
        $metricRow = MaterialRequest::selectRaw("
            COUNT(*) as total_requests,
            COALESCE(SUM(CASE WHEN status NOT IN ('CANCELLED', 'REJECTED') THEN total_estimated_cost ELSE 0 END), 0) as total_cost,
            SUM(CASE WHEN status = 'PENDING' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status IN ('APPROVED', 'PURCHASED') THEN 1 ELSE 0 END) as shipping_count,
            SUM(CASE WHEN status = 'RECEIVED' THEN 1 ELSE 0 END) as received_count,
            SUM(CASE WHEN status IN ('REJECTED', 'CANCELLED') THEN 1 ELSE 0 END) as rejected_count
        ")->first();

        $metrics = [
            'total_requests'   => (int) ($metricRow->total_requests ?? 0),
            'total_cost'       => (float) ($metricRow->total_cost ?? 0),
            'pending_count'    => (int) ($metricRow->pending_count ?? 0),
            'shipping_count'   => (int) ($metricRow->shipping_count ?? 0),
            'received_count'   => (int) ($metricRow->received_count ?? 0),
            'rejected_count'   => (int) ($metricRow->rejected_count ?? 0),
        ];

        return view('livewire.procurement.index', [
            'requests' => $requests,
            'metrics'  => $metrics,
        ])->layout('layouts.workshop-pwa');
    }
}
