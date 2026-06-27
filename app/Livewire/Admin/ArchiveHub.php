<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArchiveHub extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $activeTab = 'active'; // 'active' or 'archived'
    
    public $selectedIds = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'activeTab' => ['except' => 'active'],
    ];

    public function mount()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedSelectAll($value)
    {
        $currentPageIds = $this->getWorkOrdersQuery()
            ->paginate(25)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        if ($value) {
            $this->selectedIds = array_unique(array_merge($this->selectedIds, $currentPageIds));
        } else {
            $this->selectedIds = array_diff($this->selectedIds, $currentPageIds);
        }
    }

    public function clearSelection()
    {
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatedSelectedIds()
    {
        $currentPageIds = $this->getWorkOrdersQuery()
            ->paginate(25)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
            
        $this->selectAll = count(array_intersect($currentPageIds, $this->selectedIds)) === count($currentPageIds) && count($currentPageIds) > 0;
    }

    private function getWorkOrdersQuery()
    {
        $query = WorkOrder::query();

        if ($this->activeTab === 'active') {
            // All statuses EXCEPT SPK_PENDING and HISTORY
            $query->whereNotIn('status', [WorkOrderStatus::SPK_PENDING, WorkOrderStatus::HISTORY]);
            
            if ($this->statusFilter && $this->statusFilter !== 'all') {
                $query->where('status', $this->statusFilter);
            }
        } else {
            // Only HISTORY status
            $query->where('status', WorkOrderStatus::HISTORY);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%")
                  ->orWhere('shoe_brand', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('updated_at', 'desc');
    }

    public function archiveSelected(WorkflowService $workflow)
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih data terlebih dahulu.');
            return;
        }

        $count = count($this->selectedIds);

        DB::beginTransaction();
        try {
            $orders = WorkOrder::whereIn('id', $this->selectedIds)->get();
            
            foreach ($orders as $order) {
                if ($order->status === WorkOrderStatus::SPK_PENDING) {
                    continue; // Skip SPK pending from archiving
                }

                // Transition status to HISTORY
                $workflow->updateStatus(
                    $order, 
                    WorkOrderStatus::HISTORY, 
                    'SPK diarsipkan secara manual ke HISTORY melalui Archive Hub oleh Admin.'
                );
            }

            DB::commit();

            $this->clearSelection();
            $this->dispatch('swal:toast', icon: 'success', title: "Berhasil mengarsipkan {$count} SPK.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Archive Selected Error: " . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: 'Gagal mengarsipkan: ' . $e->getMessage());
        }
    }

    public function restoreSelected(WorkflowService $workflow)
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih data terlebih dahulu.');
            return;
        }

        $count = count($this->selectedIds);

        DB::beginTransaction();
        try {
            $orders = WorkOrder::whereIn('id', $this->selectedIds)->get();
            
            foreach ($orders as $order) {
                if ($order->status !== WorkOrderStatus::HISTORY) {
                    continue;
                }

                // Restore status: fallback to previous_status or SELESAI
                $targetStatus = $order->previous_status ?: WorkOrderStatus::SELESAI;
                
                $workflow->updateStatus(
                    $order, 
                    $targetStatus, 
                    'SPK dipulihkan kembali dari HISTORY ke ' . $targetStatus->value . ' oleh Admin.'
                );
            }

            DB::commit();

            $this->clearSelection();
            $this->dispatch('swal:toast', icon: 'success', title: "Berhasil memulihkan {$count} SPK.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Restore Selected Error: " . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: 'Gagal memulihkan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $workOrders = $this->getWorkOrdersQuery()->paginate(25);

        // Get status list for filtering
        $allStatuses = [];
        foreach (WorkOrderStatus::cases() as $case) {
            if ($case !== WorkOrderStatus::SPK_PENDING && $case !== WorkOrderStatus::HISTORY) {
                $allStatuses[$case->value] = $case->label();
            }
        }

        return view('livewire.admin.archive-hub', [
            'workOrders' => $workOrders,
            'allStatuses' => $allStatuses
        ])->layout('layouts.app');
    }
}
