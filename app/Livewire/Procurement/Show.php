<?php

namespace App\Livewire\Procurement;

use App\Models\MaterialRequest;
use App\Models\WorkOrder;
use App\Services\MaterialManagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Show extends Component
{
    public $materialRequest;
    public $requestId;

    public function mount($id)
    {
        $this->requestId = $id;
        $this->loadRequest();
    }

    public function loadRequest()
    {
        $this->materialRequest = MaterialRequest::with(['requestedBy', 'approvedBy', 'items.material', 'workOrder', 'oto'])
            ->findOrFail($this->requestId);
    }

    public function approve()
    {
        Gate::authorize('manageInventory', WorkOrder::class);

        if (!$this->materialRequest->isPending()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Hanya request PENDING yang bisa diapprove.']);
            return;
        }

        $this->materialRequest->approve(Auth::id());
        
        // Log to WorkOrderLog if applicable
        if ($this->materialRequest->work_order_id) {
            $this->materialRequest->workOrder->logs()->create([
                'step' => 'PROCUREMENT',
                'action' => 'APPROVED',
                'user_id' => Auth::id(),
                'description' => "Material Request #{$this->materialRequest->request_number} APPROVED."
            ]);
        }

        $this->loadRequest();
        $this->dispatch('notify', type: 'success', message: 'Pengajuan material berhasil disetujui.');
    }

    public function reject()
    {
        Gate::authorize('manageInventory', WorkOrder::class);

        if (!$this->materialRequest->isPending()) {
            $this->dispatch('notify', type: 'error', message: 'Hanya request PENDING yang bisa ditolak.');
            return;
        }

        $this->materialRequest->reject();

        if ($this->materialRequest->work_order_id) {
            $this->materialRequest->workOrder->logs()->create([
                'step' => 'PROCUREMENT',
                'action' => 'REJECTED',
                'user_id' => Auth::id(),
                'description' => "Material Request #{$this->materialRequest->request_number} REJECTED."
            ]);
        }

        $this->loadRequest();
        $this->dispatch('notify', type: 'error', message: 'Pengajuan material ditolak.');
    }

    public function markAsPurchased(MaterialManagementService $materialService)
    {
        Gate::authorize('manageInventory', WorkOrder::class);

        if (!$this->materialRequest->isApproved() && !$this->materialRequest->isPending()) {
            $this->dispatch('notify', type: 'error', message: 'Request harus berstatus PENDING atau APPROVED.');
            return;
        }

        DB::transaction(function () use ($materialService) {
            // 1. Mark status as PURCHASED
            $this->materialRequest->markAsPurchased();

            // 2. Increment Stock & Log Transaction using Service
            foreach ($this->materialRequest->items as $item) {
                if ($item->material) {
                    $materialService->restock(
                        $item->material,
                        $item->quantity,
                        "Penerimaan barang dari Pengajuan #{$this->materialRequest->request_number}",
                        'MaterialRequest',
                        $this->materialRequest->id
                    );
                }
            }

            // 3. Log to Work Order if applicable
            if ($this->materialRequest->work_order_id) {
                $this->materialRequest->workOrder->logs()->create([
                    'step' => 'PROCUREMENT',
                    'action' => 'PURCHASED',
                    'user_id' => Auth::id(),
                    'description' => "Material for Request #{$this->materialRequest->request_number} marked as PURCHASED. Stock updated and transaction logged."
                ]);
            }
        });

        $this->loadRequest();
        $this->dispatch('notify', type: 'success', message: 'Material ditandai sudah dibeli & Stok otomatis bertambah.');
    }

    public function cancel()
    {
        if (!$this->materialRequest->isPending()) {
            $this->dispatch('notify', type: 'error', message: 'Hanya request PENDING yang bisa dibatalkan.');
            return;
        }

        $this->materialRequest->cancel();

        if ($this->materialRequest->work_order_id) {
            $this->materialRequest->workOrder->logs()->create([
                'step' => 'PROCUREMENT',
                'action' => 'CANCELLED',
                'user_id' => Auth::id(),
                'description' => "Material Request #{$this->materialRequest->request_number} CANCELLED by user."
            ]);
        }

        $this->loadRequest();
        $this->dispatch('notify', type: 'info', message: 'Pengajuan material dibatalkan.');
    }

    public function verifyAndReceiveMaterial()
    {
        DB::transaction(function () {
            // 1. Update MaterialRequest status
            $this->materialRequest->update(['status' => 'RECEIVED']);

            // 2. Identify all related WorkOrders
            $workOrders = collect();
            if ($this->materialRequest->work_order_id && $this->materialRequest->workOrder) {
                $workOrders->push($this->materialRequest->workOrder);
            }

            foreach ($this->materialRequest->items as $item) {
                if ($item->workOrder) {
                    $workOrders->push($item->workOrder);
                }
            }

            // 3. Process each WorkOrder to set arrival date & advance status to Production
            $workflow = app(\App\Services\WorkflowService::class);

            foreach ($workOrders->unique('id') as $order) {
                $order->material_arrival_date = now();
                $order->save();

                try {
                    $workflow->updateStatus(
                        $order,
                        \App\Enums\WorkOrderStatus::PRODUCTION,
                        "Material pengajuan (#{$this->materialRequest->request_number}) diverifikasi & diterima fisik oleh " . Auth::user()->name . ". SPK dilanjutkan ke Stasiun Produksi."
                    );
                } catch (\Exception $e) {
                    // Fallback direct update
                    $order->status = \App\Enums\WorkOrderStatus::PRODUCTION;
                    $order->save();

                    $order->logs()->create([
                        'user_id' => Auth::id(),
                        'step' => 'SORTIR_BELANJA',
                        'action' => 'MATERIAL_RECEIVED_VERIFIED',
                        'description' => "Material pengajuan (#{$this->materialRequest->request_number}) diverifikasi & diterima fisik oleh " . Auth::user()->name . ". SPK dilanjutkan ke Stasiun Produksi (fallback).",
                    ]);
                }
            }
        });

        $this->loadRequest();
        $this->dispatch('notify', type: 'success', message: 'Material berhasil diverifikasi & SPK terkait otomatis dilanjutkan ke Stasiun Produksi!');
    }

    public function render()
    {
        return view('livewire.procurement.show')->layout('layouts.workshop-pwa');
    }
}
