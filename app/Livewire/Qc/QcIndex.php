<?php

namespace App\Livewire\Qc;

use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\HasStationTracking;
use App\Services\WorkflowService;

class QcIndex extends Component
{
    use WithPagination;
    use HasStationTracking;

    #[Url(except: 'jahit')]
    public $activeTab = 'jahit';

    #[Url(except: '')]
    public $search = '';

    #[Url(except: 'all')]
    public $priority = 'all';

    #[Url(except: 'all')]
    public $technicianFilter = 'all';

    #[Url(except: 'asc')]
    public $sort = 'asc';

    #[Url(except: false)]
    public $onlyInProgress = false;

    public $selectedItems = [];
    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->orders()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        $orderCount = $this->orders()->count();
        $this->selectAll = count($this->selectedItems) === $orderCount && $orderCount > 0;
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPriority() { $this->resetPage(); }
    public function updatingTechnicianFilter() { $this->resetPage(); }
    public function updatingOnlyInProgress() { $this->resetPage(); }
    public function updatingActiveTab() { $this->resetPage(); $this->selectedItems = []; $this->onlyInProgress = false; }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->selectedItems = [];
    }

    #[Computed]
    public function techs()
    {
        return [
            'jahit' => User::where('role', 'technician')->where('specialization', 'Jahit')->select('id', 'name')->get(),
            'cleanup' => User::where('role', 'technician')->whereIn('specialization', ['Clean Up', 'Washing'])->select('id', 'name')->get(),
            'final' => User::where('role', 'technician')->where('specialization', 'PIC QC')->select('id', 'name')->get(),
        ];
    }

    #[Computed]
    public function counts()
    {
        $baseQuery = WorkOrder::where('status', WorkOrderStatus::QC);

        return [
            'jahit' => (clone $baseQuery)->qcJahit()->whereNull('qc_jahit_completed_at')->count(),
            'cleanup' => (clone $baseQuery)->qcCleanup()->whereNull('qc_cleanup_completed_at')->count(),
            'final' => (clone $baseQuery)->qcFinal()->whereNull('qc_final_completed_at')->count(),
            'review' => (clone $baseQuery)->qcReview()->count(),
        ];
    }

    public function updateStation($id, $type, $action, $techId = null, $finishedAt = null)
    {
        $order = WorkOrder::find($id);
        if (!$order) return;

        try {
            $this->handleStationUpdate(
                $order, 
                $type, 
                $action, 
                Auth::id(), 
                $techId, 
                WorkOrderStatus::QC->value,
                $finishedAt
            );
            
            $order->save();
            $this->dispatch('swal:toast', icon: 'success', title: 'QC diperbarui');
        } catch (\Throwable $e) {
            Log::error('QC Update Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function bulkAction($action, $techId = null)
    {
        $workflow = app(WorkflowService::class);
        if (empty($this->selectedItems)) {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih item terlebih dahulu');
            return;
        }

        $type = match($this->activeTab) {
            'jahit' => 'qc_jahit',
            'cleanup' => 'qc_cleanup',
            'final' => 'qc_final',
            default => null
        };

        if ($action !== 'approve' && !$type) {
            $this->dispatch('swal:toast', icon: 'error', title: 'Tipe stasiun tidak valid');
            return;
        }

        $successCount = 0;
        foreach ($this->selectedItems as $id) {
            try {
                $order = WorkOrder::find($id);
                if (!$order) continue;

                if ($action === 'approve') {
                    $workflow->updateStatus($order, WorkOrderStatus::SELESAI, 'QC Approved (Bulk).');
                    if ($order->is_revising) {
                        $order->is_revising = false;
                        $order->previous_status = null;
                        $order->save();
                    }
                    $successCount++;
                } else {
                    $this->handleStationUpdate(
                        $order, 
                        $type, 
                        $action === 'assign' ? 'start' : $action, 
                        Auth::id(), 
                        $techId, 
                        WorkOrderStatus::QC->value
                    );
                    $order->save();
                    $successCount++;
                }
            } catch (\Exception $e) {
                Log::error("Bulk QC Error (#$id): " . $e->getMessage());
            }
        }

        $this->selectedItems = [];
        unset($this->orders);
        $this->dispatch('swal:toast', icon: 'success', title: "$successCount item berhasil diproses");
    }

    public function performApprove($id)
    {
        $workflow = app(WorkflowService::class);
        $order = WorkOrder::find($id);
        if ($order) {
            try {
                $workflow->updateStatus($order, WorkOrderStatus::SELESAI, 'QC Approved. Order Selesai.');
                if ($order->is_revising) {
                    $order->is_revising = false;
                    $order->previous_status = null;
                    $order->save();
                }
                unset($this->orders);
                $this->dispatch('swal:toast', icon: 'success', title: 'Berhasil diselesaikan!');
            } catch (\Exception $e) {
                $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
            }
        }
    }

    public function approveAll()
    {
        $workflow = app(\App\Services\WorkflowService::class);
        $ordersToApprove = $this->orders->items();
        
        $successCount = 0;
        foreach ($ordersToApprove as $order) {
            try {
                $workflow->updateStatus($order, WorkOrderStatus::SELESAI, 'QC Approved. Order Selesai.');
                if ($order->is_revising) {
                    $order->is_revising = false;
                    $order->previous_status = null;
                    $order->save();
                }
                $successCount++;
            } catch (\Exception $e) {
                Log::error("Approve All QC Error (#{$order->id}): " . $e->getMessage());
            }
        }
        
        unset($this->orders);
        $this->dispatch('swal:toast', icon: 'success', title: "$successCount antrean berhasil disetujui");
    }

    #[Computed]
    public function orders()
    {
        $query = WorkOrder::query()
            ->with(['customer', 'workOrderServices', 'qcJahitBy', 'qcCleanupBy', 'qcFinalBy', 'cxIssues', 'photos', 'invoice']);

        // Base Status Filter (QC)
        $query->where('status', WorkOrderStatus::QC);

        // Search Filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('shoe_brand', 'like', '%' . $this->search . '%');
            });
        }

        // Tab Filter
        if ($this->activeTab !== 'review') {
            $query->where(function($q) {
                if ($this->activeTab === 'jahit') {
                    $q->qcJahit()->whereNull('qc_jahit_completed_at');
                } elseif ($this->activeTab === 'cleanup') {
                    $q->qcCleanup()->whereNull('qc_cleanup_completed_at');
                } elseif ($this->activeTab === 'final') {
                    $q->qcFinal()->whereNull('qc_final_completed_at');
                }
            });
        }

        // Only In Progress Filter
        if ($this->onlyInProgress && $this->activeTab !== 'review') {
            $prefix = "qc_{$this->activeTab}";
            $query->whereNotNull("{$prefix}_by")
                  ->whereNotNull("{$prefix}_started_at")
                  ->whereNull("{$prefix}_completed_at");
        }

        // Priority Filter
        if ($this->priority !== 'all') {
            if ($this->priority === 'urgent') {
                $query->whereIn('priority', ['Prioritas', 'Urgent', 'Express', 'OTO']);
            } else {
                $query->where('priority', 'Regular');
            }
        }

        // Technician Filter
        if ($this->technicianFilter !== 'all' && $this->activeTab !== 'review') {
            $column = "qc_{$this->activeTab}_by";
            $query->where($column, $this->technicianFilter);
        }

        // Sorting
        $startedColumn = match($this->activeTab) {
            'jahit' => 'qc_jahit_started_at',
            'cleanup' => 'qc_cleanup_started_at',
            'final' => 'qc_final_started_at',
            default => null
        };
        
        $query->orderByRaw("CASE WHEN fast_track_status = 'yes' THEN 0 ELSE 1 END");
        if ($startedColumn) {
            $query->orderByRaw("CASE WHEN $startedColumn IS NOT NULL THEN 0 ELSE 1 END");
        }
        $query->orderByRaw("CASE WHEN priority IN ('Prioritas', 'Urgent', 'Express', 'OTO') THEN 0 ELSE 1 END");
        $query->orderBy('id', $this->sort === 'desc' ? 'desc' : 'asc');

        if ($this->activeTab === 'review' && empty($this->search)) {
            // Use SQL scope instead of collection filtering
            $query->qcReview();
        }

        return $query->paginate(50);
    }

    public function render()
    {
        return view('livewire.qc.qc-index', [
            'orders' => $this->orders
        ])->layout('layouts.app');
    }
}
