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

    #[Url(except: 'qc')]
    public $activeTab = 'qc';

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
        $allTechs = User::where('is_active', true)
            ->where(function($q) {
                $q->where('role', 'technician')->orWhere('role', 'pic');
            })
            ->select('id', 'name', 'specialization')
            ->orderBy('name')
            ->get();

        return [
            'jahit' => $allTechs,
            'cleanup' => $allTechs,
            'final' => $allTechs,
        ];
    }

    #[Computed]
    public function counts()
    {
        $baseQuery = WorkOrder::where('status', WorkOrderStatus::QC);

        return [
            'qc' => (clone $baseQuery)->where(function($q) {
                $q->whereNull('qc_cleanup_completed_at')
                  ->orWhereNull('qc_final_completed_at')
                  ->orWhere(function($sq) {
                      $sq->withServiceCategory(WorkOrder::CAT_SOL)
                        ->whereNull('qc_jahit_completed_at');
                  });
            })->count(),
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
            unset($this->orders);
            $this->dispatch('swal:toast', icon: 'success', title: 'QC diperbarui');
        } catch (\Throwable $e) {
            Log::error('QC Update Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function updateTechnician($id, $type, $techId)
    {
        $order = WorkOrder::find($id);
        if (!$order) return;

        $column = "{$type}_by";
        $oldTechName = $order->{$type . 'By'}->name ?? 'Belum ditugaskan';
        $order->{$column} = $techId ?: null;
        $order->save();

        $newTechName = $techId ? (User::find($techId)->name ?? '-') : 'Dikosongkan';

        // Audit Log
        $order->logs()->create([
            'user_id'     => Auth::id(),
            'step'        => 'QC',
            'action'      => 'TECHNICIAN_ASSIGNED',
            'description' => "Teknisi stasiun {$type} diubah dari '{$oldTechName}' menjadi '{$newTechName}'",
        ]);

        unset($this->orders);
        $this->dispatch('swal:toast', icon: 'success', title: 'Teknisi berhasil diperbarui');
    }

    public function autoAssignUnassignedTechnicians()
    {
        $unassignedOrders = WorkOrder::where('status', WorkOrderStatus::QC)
            ->where(function($q) {
                $q->whereNull('qc_jahit_by')
                  ->orWhereNull('qc_cleanup_by')
                  ->orWhereNull('qc_final_by');
            })
            ->get();

        if ($unassignedOrders->isEmpty()) {
            $this->dispatch('swal:toast', icon: 'info', title: 'Seluruh SPK aktif di QC sudah terisi teknisinya.');
            return;
        }

        $service = app(\App\Services\TechnicianAssignmentService::class);
        $assignedCount = 0;

        foreach ($unassignedOrders as $order) {
            $service->autoAssignQcTechnicians($order);
            $assignedCount++;
        }

        unset($this->orders);
        $this->dispatch('swal:toast', icon: 'success', title: "Auto-assign berhasil! {$assignedCount} SPK telah diisikan teknisinya (teknisi yang sudah ada dilewati).");
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
                    $workflow->updateStatus($order, WorkOrderStatus::STAGING_OUTBOUND, 'Lolos QC Akhir (Bulk). Pindah ke Staging Outbound.');
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
                $workflow->updateStatus($order, WorkOrderStatus::STAGING_OUTBOUND, 'Lolos QC Akhir. Pindah ke Staging Outbound.');
                if ($order->is_revising) {
                    $order->is_revising = false;
                    $order->previous_status = null;
                    $order->save();
                }
                unset($this->orders);
                $this->dispatch('swal:toast', icon: 'success', title: 'Lolos QC Akhir! SPK dipindahkan ke Staging Outbound.');
            } catch (\Exception $e) {
                $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
            }
        }
    }

    public function performRevision($id, $reason = 'Revisi dari QC Akhir')
    {
        $order = WorkOrder::find($id);
        if ($order) {
            try {
                // Reset QC completion flags so work can be redone
                $order->qc_jahit_completed_at = null;
                $order->qc_cleanup_completed_at = null;
                $order->qc_final_completed_at = null;
                $order->is_revising = true;
                $order->save();

                // Create WorkOrderRevision log
                \App\Models\WorkOrderRevision::create([
                    'work_order_id' => $order->id,
                    'status'        => 'OPEN',
                    'origin_status' => 'POST_QC',
                    'qc_stage'      => 'AKHIR',
                    'reason'        => $reason,
                    'created_by'    => Auth::id(),
                ]);

                // Record audit log
                $order->logs()->create([
                    'user_id'     => Auth::id(),
                    'step'        => 'QC',
                    'action'      => 'REVISION_REQUESTED',
                    'description' => "Revisi QC Akhir: {$reason}",
                ]);

                unset($this->orders);
                $this->dispatch('swal:toast', icon: 'warning', title: 'SPK dikembalikan ke antrean QC untuk revisi.');
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
                $workflow->updateStatus($order, WorkOrderStatus::STAGING_OUTBOUND, 'Lolos QC Akhir. Pindah ke Staging Outbound.');
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
        $this->dispatch('swal:toast', icon: 'success', title: "$successCount antrean berhasil disetujui (Lolos QC Akhir)");
    }

    #[Computed]
    public function orders()
    {
        $query = WorkOrder::query()
            ->with(['customer', 'workOrderServices', 'qcJahitBy', 'qcCleanupBy', 'qcFinalBy', 'cxIssues', 'photos', 'invoice', 'logs', 'revisions']);

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
        if ($this->activeTab === 'qc') {
            $query->where(function($q) {
                $q->whereNull('qc_cleanup_completed_at')
                  ->orWhereNull('qc_final_completed_at')
                  ->orWhere(function($sq) {
                      $sq->withServiceCategory(WorkOrder::CAT_SOL)
                        ->whereNull('qc_jahit_completed_at');
                  });
            });
        } elseif ($this->activeTab === 'review') {
            $query->qcReview();
        }

        // Only In Progress Filter
        if ($this->onlyInProgress && $this->activeTab === 'qc') {
            $query->where(function($q) {
                $q->where(function($q2) {
                    $q2->whereNotNull('qc_jahit_started_at')->whereNull('qc_jahit_completed_at');
                })->orWhere(function($q2) {
                    $q2->whereNotNull('qc_cleanup_started_at')->whereNull('qc_cleanup_completed_at');
                })->orWhere(function($q2) {
                    $q2->whereNotNull('qc_final_started_at')->whereNull('qc_final_completed_at');
                });
            });
        }

        // Priority Filter
        if ($this->priority !== 'all') {
            if ($this->priority === 'urgent') {
                $query->whereIn('priority', ['Prioritas', 'Urgent', 'Express', 'OTO']);
            } else {
                $query->where('priority', 'Regular');
            }
        }

        // Sorting
        $query->orderByRaw("CASE WHEN EXISTS (SELECT 1 FROM cx_issues WHERE cx_issues.work_order_id = work_orders.id AND cx_issues.status = 'RESOLVED') THEN 0 ELSE 1 END");
        $query->orderByRaw("CASE WHEN fast_track_status = 'yes' THEN 0 ELSE 1 END");
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
        ])->layout('layouts.workshop-pwa');
    }
}
