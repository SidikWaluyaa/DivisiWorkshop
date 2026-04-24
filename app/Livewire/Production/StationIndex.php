<?php

namespace App\Livewire\Production;

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

class StationIndex extends Component
{
    use WithPagination;
    use HasStationTracking;

    #[Url(except: 'sol')]
    public $activeTab = 'sol';

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
            $this->selectedItems = $this->orders->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        $this->selectAll = count($this->selectedItems) === $this->orders->count() && $this->orders->count() > 0;
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
            'sol' => User::where('role', 'technician')->where('specialization', 'Sol Repair')->select('id', 'name')->get(),
            'upper' => User::where('role', 'technician')->where('specialization', 'Upper Repair')->select('id', 'name')->get(),
            'treatment' => User::where('role', 'technician')->whereIn('specialization', ['Washing', 'Repaint', 'Treatment', 'Clean Up'])->select('id', 'name')->get(),
        ];
    }

    #[Computed]
    public function counts()
    {
        return [
            'sol' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)->productionSol()->whereNull('prod_sol_completed_at')->count(),
            'upper' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)->productionUpper()->whereNull('prod_upper_completed_at')->count(),
            'treatment' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)->productionTreatment()->whereNull('prod_cleaning_completed_at')->count(),
            'review' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)->get()->filter(fn($o) => $o->is_production_finished)->count(),
        ];
    }

    public function updateStation($id, $type, $action, $techId = null, $finishedAt = null)
    {
        $order = WorkOrder::find($id);
        if (!$order) return;

        try {
            // Apply Trait Logic (handleStationUpdate is from HasStationTracking)
            $this->handleStationUpdate(
                $order, 
                $type, 
                $action, 
                Auth::id(), 
                $techId, 
                WorkOrderStatus::PRODUCTION->value,
                $finishedAt
            );
            
            // Check authorization (using Controller logic manually or via Policy)
            // For now, simple check:
            if (!Auth::user()->can('updateProduction', $order)) {
                throw new \Exception('Unauthorized action.');
            }
            
            $order->save();
            
            // Note: checkOverallCompletion logic from controller is basically determining if we auto-move to QC.
            // Since it was commented out in controller, I won't re-enable it unless requested.
            
            $this->dispatch('swal:toast', icon: 'success', title: 'Status diperbarui');
        } catch (\Throwable $e) {
            Log::error('Production Update Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function bulkAction($action, $techId = null)
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih item terlebih dahulu');
            return;
        }

        $workflow = app(\App\Services\WorkflowService::class);
        $type = match($this->activeTab) {
            'sol' => 'prod_sol',
            'upper' => 'prod_upper',
            'treatment' => 'prod_cleaning',
            default => null
        };

        if ($action !== 'approve' && !$type) {
            $this->dispatch('swal:toast', icon: 'error', title: 'Tipe stasiun tidak valid untuk aksi ini');
            return;
        }

        $successCount = 0;
        foreach ($this->selectedItems as $id) {
            try {
                $order = WorkOrder::find($id);
                if (!$order) continue;

                if ($action === 'approve') {
                    if (Auth::user()->can('approveProduction', $order)) {
                        $workflow->updateStatus($order, WorkOrderStatus::QC, 'Bulk approval from Production.');
                        $successCount++;
                    }
                } else {
                    if (Auth::user()->can('updateProduction', $order)) {
                        $this->handleStationUpdate(
                            $order, 
                            $type, 
                            $action === 'assign' ? 'start' : $action, 
                            Auth::id(), 
                            $techId ?: Auth::id(), 
                            WorkOrderStatus::PRODUCTION->value
                        );
                        $order->save();
                        $successCount++;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Bulk Action Error (#$id): " . $e->getMessage());
            }
        }

        $this->selectedItems = [];
        $this->dispatch('swal:toast', icon: 'success', title: "$successCount item berhasil diproses");
    }

    public function performApprove($id, \App\Services\WorkflowService $workflow)
    {
        $order = WorkOrder::find($id);
        if ($order) {
            if (!Auth::user()->can('approveProduction', $order)) {
                $this->dispatch('swal:toast', icon: 'error', title: 'Unauthorized');
                return;
            }
            $workflow->updateStatus($order, WorkOrderStatus::QC, 'Produksi selesai & disetujui Admin.');
            $this->dispatch('swal:toast', icon: 'success', title: 'Berhasil di-approve ke QC');
        }
    }

    #[Computed]
    public function orders()
    {
        $query = WorkOrder::query()
            ->with(['customer', 'workOrderServices', 'prodSolBy', 'prodUpperBy', 'prodCleaningBy', 'cxIssues', 'photos']);

        // Search Filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('shoe_brand', 'like', '%' . $this->search . '%');
            });
        }

        // Base Filter: Only show items in PRODUCTION status
        $query->where('status', WorkOrderStatus::PRODUCTION->value);

        // Tab Filter
        if ($this->activeTab !== 'review') {
            $query->where(function($q) {
                if ($this->activeTab === 'sol') {
                    $q->productionSol()->whereNull('prod_sol_completed_at');
                } elseif ($this->activeTab === 'upper') {
                    $q->productionUpper()->whereNull('prod_upper_completed_at');
                } elseif ($this->activeTab === 'treatment') {
                    $q->productionTreatment()->whereNull('prod_cleaning_completed_at');
                }
            });
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
            $column = match($this->activeTab) {
                'upper' => 'prod_upper_by',
                'treatment' => 'prod_cleaning_by',
                default => 'prod_sol_by'
            };
            $query->where($column, $this->technicianFilter);
        }

        // Apply Sorting
        // 1. Prioritize Started Items (Items that are currently being worked on)
        $startedColumn = match($this->activeTab) {
            'sol' => 'prod_sol_started_at',
            'upper' => 'prod_upper_started_at',
            'treatment' => 'prod_cleaning_started_at',
            default => null
        };
        
        if ($startedColumn) {
            $query->orderByRaw("CASE WHEN $startedColumn IS NOT NULL THEN 0 ELSE 1 END");
        }

        // 2. Then by Priority
        $query->orderByRaw("CASE WHEN priority IN ('Prioritas', 'Urgent', 'Express') THEN 0 ELSE 1 END");

        // 3. Then by custom sort (Latest/Oldest)
        $query->orderBy('id', $this->sort === 'desc' ? 'desc' : 'asc');

        if ($this->activeTab === 'review' && empty($this->search)) {
            return $query->get()->filter(fn($o) => $o->is_production_finished);
        }

        // Reduced per-page to 50 for faster rendering of cards
        return $query->paginate(50);
    }

    public function render()
    {
        return view('livewire.production.station-index', [
            'orders' => $this->orders
        ])->layout('layouts.app');
    }
}
