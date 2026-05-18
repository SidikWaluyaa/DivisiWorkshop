<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WaitingPayment extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function dispatchSpk(WorkflowService $workflowService, $id)
    {
        try {
            $order = WorkOrder::findOrFail($id);

            // Validasi status saat ini harus WAITING_PAYMENT
            if ($order->status !== WorkOrderStatus::WAITING_PAYMENT->value && $order->status !== WorkOrderStatus::WAITING_PAYMENT) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Status SPK tidak valid untuk dilanjutkan.']);
                return;
            }

            // Gunakan WorkflowService untuk update status
            $workflowService->updateStatus(
                $order, 
                WorkOrderStatus::READY_TO_DISPATCH, 
                "Dilanjutkan ke Logistik oleh Finance (Manual Livewire)",
                Auth::id()
            );

            $this->dispatch('notify', ['type' => 'success', 'message' => "SPK #{$order->spk_number} berhasil dilanjutkan ke Ready to Dispatch."]);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $query = WorkOrder::where('status', WorkOrderStatus::WAITING_PAYMENT->value)
            ->with(['customer']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('spk_number', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('livewire.finance.waiting-payment', [
            'orders' => $orders
        ])->layout('layouts.app');
    }
}
