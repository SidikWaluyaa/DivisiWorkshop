<?php

namespace App\Livewire\Preparation;

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

class PrepIndex extends Component
{
    use WithPagination;
    use HasStationTracking;

    #[Url(except: 'washing')]
    public $activeTab = 'washing';

    #[Url(except: '')]
    public $search = '';

    #[Url(except: 'all')]
    public $priority = 'all';

    #[Url(except: 'all')]
    public $technicianFilter = 'all';

    #[Url(except: 'asc')]
    public $sort = 'asc';

    public $selectedItems = [];
    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->orders()->get()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        $this->selectAll = count($this->selectedItems) === $this->orders()->count() && $this->orders()->count() > 0;
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPriority() { $this->resetPage(); }
    public function updatingTechnicianFilter() { $this->resetPage(); }
    public function updatingActiveTab() { $this->resetPage(); $this->selectedItems = []; }

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
            'washing' => User::whereIn('specialization', ['Washing', 'Treatment', 'Clean Up'])->select('id', 'name')->get(),
            'sol' => User::whereIn('specialization', ['Sol Repair', 'PIC Material Sol'])->select('id', 'name')->get(),
            'upper' => User::whereIn('specialization', ['Upper Repair', 'Repaint', 'Jahit', 'PIC Material Upper'])->select('id', 'name')->get(),
            'review' => User::where('role', 'admin')->select('id', 'name')->get(),
        ];
    }

    #[Computed]
    public function counts()
    {
        $baseQuery = WorkOrder::where('status', WorkOrderStatus::PREPARATION);

        return [
            'washing' => (clone $baseQuery)->whereNull('prep_washing_completed_at')->count(),
            'sol' => (clone $baseQuery)->withServiceCategory(WorkOrder::CAT_SOL)
                        ->whereNull('prep_sol_completed_at')
                        ->count(),
            'upper' => (clone $baseQuery)->withServiceCategory([WorkOrder::CAT_UPPER, WorkOrder::CAT_REPAINT])
                        ->whereNull('prep_upper_completed_at')
                        ->count(),
            'review' => (clone $baseQuery)->get()->filter(fn($o) => $o->is_ready)->count(),
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
                WorkOrderStatus::PREPARATION->value,
                $finishedAt
            );
            
            $order->save();
            $this->dispatch('swal:toast', icon: 'success', title: 'Preparation diperbarui');
        } catch (\Throwable $e) {
            Log::error('Preparation Update Error: ' . $e->getMessage());
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
            'washing' => 'washing',
            'sol' => 'sol',
            'upper' => 'upper',
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
                    $this->performApproveLogic($order, $workflow);
                    $successCount++;
                } else {
                    $this->handleStationUpdate(
                        $order, 
                        "prep_{$type}", 
                        $action === 'assign' ? 'start' : $action, 
                        Auth::id(), 
                        $techId ?: Auth::id(), 
                        WorkOrderStatus::PREPARATION->value
                    );
                    $order->save();
                    $successCount++;
                }
            } catch (\Exception $e) {
                Log::error("Bulk Preparation Error (#$id): " . $e->getMessage());
            }
        }

        $this->selectedItems = [];
        $this->dispatch('swal:toast', icon: 'success', title: "$successCount item berhasil diproses");
    }

    public function performApprove($id)
    {
        $workflow = app(WorkflowService::class);
        $order = WorkOrder::find($id);
        if ($order) {
            try {
                $this->performApproveLogic($order, $workflow);
                $this->dispatch('swal:toast', icon: 'success', title: 'Berhasil disetujui!');
            } catch (\Exception $e) {
                $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
            }
        }
    }

    private function performApproveLogic(WorkOrder $order, WorkflowService $workflow)
    {
        // Boomerang Logic: If in revision, jump back to previous status
        if ($order->is_revising && $order->previous_status) {
            $targetStatus = $order->previous_status; 
            $note = "Revision completed in Preparation. Returning to " . $targetStatus->value;
            
            $workflow->updateStatus($order, $targetStatus, $note);

            $order->is_revising = false;
            $order->previous_status = null;
            $order->save();
            return;
        } 
        
        if ($order->is_revising) {
            $order->is_revising = false;
            $order->save();
        }

        $workflow->updateStatus($order, WorkOrderStatus::SORTIR, 'Preparation Approved by Admin. Proceed to Sortir.');
    }

    #[Computed]
    public function orders()
    {
        $query = WorkOrder::query()
            ->with(['customer', 'workOrderServices', 'prepWashingBy', 'prepSolBy', 'prepUpperBy', 'cxIssues', 'photos'])
            ->where('status', WorkOrderStatus::PREPARATION);

        // Search Filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('shoe_brand', 'like', '%' . $this->search . '%');
            });
        }

        // Tab Filter
        if ($this->activeTab === 'washing') {
            $query->whereNull('prep_washing_completed_at');
        } elseif ($this->activeTab === 'sol') {
            $query->withServiceCategory(WorkOrder::CAT_SOL)
                  ->whereNull('prep_sol_completed_at');
        } elseif ($this->activeTab === 'upper') {
            $query->withServiceCategory([WorkOrder::CAT_UPPER, WorkOrder::CAT_REPAINT])
                  ->whereNull('prep_upper_completed_at');
        } elseif ($this->activeTab === 'review') {
            // Use collection filtering for complex logic in getIsReadyAttribute
            return $query->get()->filter(fn($o) => $o->is_ready);
        }

        // Priority Filter
        if ($this->priority !== 'all') {
            if ($this->priority === 'urgent') {
                $query->whereIn('priority', ['Prioritas', 'Urgent', 'Express']);
            } else {
                $query->where('priority', 'Regular');
            }
        }

        // Technician Filter
        if ($this->technicianFilter !== 'all' && $this->activeTab !== 'review') {
            $column = "prep_{$this->activeTab}_by";
            $query->where($column, $this->technicianFilter);
        }

        // Sorting
        $startedColumn = match($this->activeTab) {
            'washing' => 'prep_washing_started_at',
            'sol' => 'prep_sol_started_at',
            'upper' => 'prep_upper_started_at',
            default => null
        };
        
        if ($startedColumn) {
            $query->orderByRaw("CASE WHEN $startedColumn IS NOT NULL THEN 0 ELSE 1 END");
        }
        $query->orderByRaw("CASE WHEN priority IN ('Prioritas', 'Urgent', 'Express') THEN 0 ELSE 1 END");
        $query->orderBy('id', $this->sort === 'desc' ? 'desc' : 'asc');

        return $query->paginate(50);
    }

    public function render()
    {
        return view('livewire.preparation.prep-index', [
            'orders' => $this->orders
        ])->layout('layouts.app');
    }
}
