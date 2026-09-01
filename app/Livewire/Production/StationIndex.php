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

    #[Url(except: 'reparasi')]
    public $activeTab = 'reparasi';

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
        $allTechs = User::where('role', 'technician')
            ->select('id', 'name', 'station', 'specialization')
            ->orderBy('name', 'asc')
            ->get();

        return [
            'sol' => $allTechs->filter(fn($u) => $u->station === 'SOLING' || empty($u->station)),
            'upper' => $allTechs->filter(fn($u) => $u->station === 'UPPER' || empty($u->station)),
            'treatment' => $allTechs->filter(fn($u) => $u->station === 'TREATMENT' || empty($u->station)),
            'prep' => $allTechs->filter(fn($u) => $u->station === 'PREPARATION'),
            'qc' => $allTechs->filter(fn($u) => $u->station === 'QC'),
            'all' => $allTechs,
        ];
    }

    #[Url(except: 'all')]
    public $substate = 'all';

    public function setSubstate($substate)
    {
        $this->substate = $substate;
        $this->resetPage();
    }

    #[Computed]
    public function counts()
    {
        $baseQuery = WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
            ->whereDoesntHave('logs', function($lq) {
                $lq->where('step', 'PRODUCTION')
                   ->where('action', 'PRODUCTION_APPROVED');
            });

        $reviewCount = (clone $baseQuery)->productionReview()->count();
        $totalProduction = (clone $baseQuery)->count();
        $reparasiCount = max(0, $totalProduction - $reviewCount);

        $reparasiQuery = (clone $baseQuery)->where(function ($q) {
            $q->whereHas('workOrderServices')
              ->where(function ($sq) {
                  $sq->where(function ($ssq) {
                      $ssq->whereHas('workOrderServices', fn($x) => $x->where('category_name', 'like', '%Sol%'))
                          ->whereNull('prod_sol_completed_at');
                  })
                  ->orWhere(function ($ssq) {
                      $ssq->whereHas('workOrderServices', fn($x) => $x->where('category_name', 'like', '%Upper%'))
                          ->whereNull('prod_upper_completed_at');
                  })
                  ->orWhere(function ($ssq) {
                      $ssq->whereHas('workOrderServices', fn($x) => $x->where('category_name', 'not like', '%Sol%')->where('category_name', 'not like', '%Upper%'))
                          ->whereNull('prod_cleaning_completed_at');
                  });
              });
        });

        $inProgressCount = (clone $reparasiQuery)->where(function($q) {
            $q->where(function($sq) {
                $sq->whereNotNull('prod_sol_started_at')->whereNull('prod_sol_completed_at');
            })
            ->orWhere(function($sq) {
                $sq->whereNotNull('prod_upper_started_at')->whereNull('prod_upper_completed_at');
            })
            ->orWhere(function($sq) {
                $sq->whereNotNull('prod_cleaning_started_at')->whereNull('prod_cleaning_completed_at');
            });
        })->count();

        $queuedCount = max(0, $reparasiCount - $inProgressCount);

        return [
            'reparasi' => $reparasiCount,
            'review' => $reviewCount,
            'in_progress' => $inProgressCount,
            'queued' => $queuedCount,
        ];
    }

    public function updateTechnician($id, $type, $techId)
    {
        $order = WorkOrder::find($id);
        if (!$order) return;

        try {
            $columnPrefix = $type;
            $oldTechId = $order->{"{$columnPrefix}_by"};
            $oldTechName = $oldTechId ? User::find($oldTechId)?->name : 'Kosong';

            $order->{"{$columnPrefix}_by"} = $techId ? (int)$techId : null;
            $order->save();

            $techName = $techId ? User::find($techId)?->name : 'Dihapus';
            $stationLabel = $this->formatStationName($type);

            // Audit trail log
            $order->logs()->create([
                'user_id'     => Auth::id(),
                'step'        => 'PRODUCTION',
                'action'      => 'TECHNICIAN_UPDATED',
                'description' => "Teknisi {$stationLabel} diubah dari [{$oldTechName}] ke [{$techName}].",
            ]);

            $this->dispatch('swal:toast', icon: 'success', title: "Teknisi {$stationLabel} diubah ke {$techName}");
        } catch (\Exception $e) {
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function updateTechnicianWithReason($id, $type, $techId, $reason)
    {
        $order = WorkOrder::find($id);
        if (!$order) return;

        if (empty(trim($reason)) || mb_strlen(trim($reason)) < 5) {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Alasan override wajib diisi minimal 5 karakter.');
            return;
        }

        try {
            $columnPrefix = $type;
            $oldTechId = $order->{"{$columnPrefix}_by"};
            $oldTechName = $oldTechId ? User::find($oldTechId)?->name : 'Kosong';

            $order->{"{$columnPrefix}_by"} = $techId ? (int)$techId : null;
            $order->save();

            $techName = $techId ? User::find($techId)?->name : 'Dihapus';
            $stationLabel = $this->formatStationName($type);

            // Audit trail log with mandatory reason
            $order->logs()->create([
                'user_id'     => Auth::id(),
                'step'        => 'PRODUCTION',
                'action'      => 'TECHNICIAN_OVERRIDE',
                'description' => "[OVERRIDE] Teknisi {$stationLabel} diubah dari [{$oldTechName}] ke [{$techName}] saat stasiun sudah berjalan. Alasan: {$reason}",
            ]);

            $this->dispatch('swal:toast', icon: 'success', title: "Override berhasil — Teknisi {$stationLabel} diubah ke {$techName}");
        } catch (\Exception $e) {
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
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

        if ($action === 'finish_active') {
            $successCount = 0;
            $skippedCount = 0;
            foreach ($this->selectedItems as $id) {
                try {
                    $order = WorkOrder::find($id);
                    if (!$order) continue;

                    $hasSol = $order->workOrderServices->contains(fn($s) => in_array($s->category_name, ['Sol']));
                    $hasUpper = $order->workOrderServices->contains(fn($s) => in_array($s->category_name, ['Upper']));
                    $hasTreatment = $order->workOrderServices->contains(fn($s) => in_array($s->category_name, ['Repaint', 'Cleaning', 'Treatment', 'Whitening']));

                    $processed = false;

                    // 1. Soling (Sol)
                    if ($hasSol && !$order->prod_sol_completed_at) {
                        if ($order->prod_sol_by) {
                            $this->handleStationUpdate($order, 'prod_sol', 'finish', Auth::id(), null, WorkOrderStatus::PRODUCTION->value);
                            $processed = true;
                        }
                    }

                    // 2. Upper (only if not locked: Soling completed or not required)
                    $isUpperLocked = $hasSol && !$order->prod_sol_completed_at;
                    if ($hasUpper && !$order->prod_upper_completed_at && !$isUpperLocked) {
                        if ($order->prod_upper_by) {
                            $this->handleStationUpdate($order, 'prod_upper', 'finish', Auth::id(), null, WorkOrderStatus::PRODUCTION->value);
                            $processed = true;
                        }
                    }

                    // 3. Treatment (only if not locked: Soling & Upper completed or not required)
                    $isTreatmentLocked = ($hasSol && !$order->prod_sol_completed_at) || ($hasUpper && !$order->prod_upper_completed_at);
                    if ($hasTreatment && !$order->prod_cleaning_completed_at && !$isTreatmentLocked) {
                        if ($order->prod_cleaning_by) {
                            $this->handleStationUpdate($order, 'prod_cleaning', 'finish', Auth::id(), null, WorkOrderStatus::PRODUCTION->value);
                            $processed = true;
                        }
                    }

                    if ($processed) {
                        $order->save();
                        $successCount++;
                    } else {
                        $skippedCount++;
                    }
                } catch (\Exception $e) {
                    Log::error("Bulk Finish Error (#$id): " . $e->getMessage());
                }
            }

            $this->selectedItems = [];
            unset($this->orders);
            $msg = "$successCount stasiun SPK berhasil diselesaikan.";
            if ($skippedCount > 0) {
                $msg .= " ($skippedCount SPK belum ada teknisi / terkunci urutan)";
            }
            $this->dispatch('swal:toast', icon: 'success', title: $msg);
        } elseif ($action === 'approve') {
            $successCount = 0;
            foreach ($this->selectedItems as $id) {
                try {
                    $order = WorkOrder::find($id);
                    if (!$order) continue;

                    if (Auth::user()->can('approveProduction', $order)) {
                        if ($order->is_revising && $order->previous_status instanceof WorkOrderStatus) {
                            $targetStatus = $order->previous_status;
                            $workflow->updateStatus($order, $targetStatus, 'Bulk revision completed in Production.');
                            $order->is_revising = false;
                            $order->previous_status = null;
                            $order->save();
                        } else {
                            if ($order->is_revising) {
                                $order->is_revising = false;
                                $order->save();
                            }
                            $order->update([
                                'current_location' => 'Produksi (Siap Handover)',
                            ]);
                            $order->logs()->create([
                                'user_id' => Auth::id(),
                                'step' => 'PRODUCTION',
                                'action' => 'PRODUCTION_APPROVED',
                                'description' => 'Produksi selesai & disetujui Admin. Siap serah terima ke QC via Surat Jalan.',
                            ]);
                        }
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    Log::error("Bulk Action Error (#$id): " . $e->getMessage());
                }
            }

            $this->selectedItems = [];
            unset($this->orders);
            $this->dispatch('swal:toast', icon: 'success', title: "$successCount SPK berhasil diapprove di Produksi.");
        }
    }

    public function autoAssignUnassignedTechnicians()
    {
        $unassignedOrders = WorkOrder::where('status', WorkOrderStatus::PRODUCTION)
            ->where(function($q) {
                $q->whereNull('prod_sol_by')
                  ->orWhereNull('prod_upper_by')
                  ->orWhereNull('prod_cleaning_by')
                  ->orWhereNull('prod_sol_started_at')
                  ->orWhereNull('prod_upper_started_at')
                  ->orWhereNull('prod_cleaning_started_at');
            })
            ->get();

        if ($unassignedOrders->isEmpty()) {
            $this->dispatch('swal:toast', icon: 'info', title: 'Seluruh SPK aktif di Produksi sudah terisi teknisi.');
            return;
        }

        $service = app(\App\Services\TechnicianAssignmentService::class);
        $assignedCount = 0;

        foreach ($unassignedOrders as $order) {
            $service->autoAssignProductionTechnicians($order, true);
            $assignedCount++;
        }

        unset($this->orders);
        $this->dispatch('swal:toast', icon: 'success', title: "Auto-assign berhasil! $assignedCount SPK telah disesuaikan dengan skill teknisi.");
    }

    public function autoAssignSingleOrder($orderId)
    {
        $order = WorkOrder::find($orderId);
        if (!$order) return;

        $service = app(\App\Services\TechnicianAssignmentService::class);
        $service->autoAssignProductionTechnicians($order, true);

        unset($this->orders);
        $this->dispatch('swal:toast', icon: 'success', title: "Auto-assign SPK #{$order->spk_number} berhasil disesuaikan dengan skill & stasiun teknisi.");
    }

    public function performApprove($id, \App\Services\WorkflowService $workflow)
    {
        $order = WorkOrder::find($id);
        if ($order) {
            if (!Auth::user()->can('approveProduction', $order)) {
                $this->dispatch('swal:toast', icon: 'error', title: 'Unauthorized');
                return;
            }

            if ($order->is_revising && $order->previous_status instanceof WorkOrderStatus) {
                $targetStatus = $order->previous_status;
                $statusLabel = $targetStatus->value;
                $note = "Revision completed in Production. Returning to " . $statusLabel;
                
                $workflow->updateStatus($order, $targetStatus, $note);

                $order->is_revising = false;
                $order->previous_status = null;
                $order->save();
            } else {
                if ($order->is_revising) {
                    $order->is_revising = false;
                    $order->save();
                }
                $order->update([
                    'current_location' => 'Produksi (Siap Handover)',
                ]);
                $order->logs()->create([
                    'user_id' => Auth::id(),
                    'step' => 'PRODUCTION',
                    'action' => 'PRODUCTION_APPROVED',
                    'description' => 'Produksi selesai & disetujui Admin. Siap serah terima ke QC via Surat Jalan.',
                ]);
            }

            unset($this->orders);
            $this->dispatch('swal:toast', icon: 'success', title: 'Berhasil di-approve ke QC');
        }
    }

    public function approveAll()
    {
        $workflow = app(\App\Services\WorkflowService::class);
        $ordersToApprove = $this->orders->items();
        
        $successCount = 0;
        foreach ($ordersToApprove as $order) {
            try {
                if (!Auth::user()->can('approveProduction', $order)) {
                    continue;
                }

                if ($order->is_revising && $order->previous_status instanceof WorkOrderStatus) {
                    $targetStatus = $order->previous_status;
                    $statusLabel = $targetStatus->value;
                    $note = "Revision completed in Production. Returning to " . $statusLabel;
                    
                    $workflow->updateStatus($order, $targetStatus, $note);

                    $order->is_revising = false;
                    $order->previous_status = null;
                    $order->save();
                } else {
                    if ($order->is_revising) {
                        $order->is_revising = false;
                        $order->save();
                    }
                    $order->update([
                        'current_location' => 'Produksi (Siap Handover)',
                    ]);
                    $order->logs()->create([
                        'user_id' => Auth::id(),
                        'step' => 'PRODUCTION',
                        'action' => 'PRODUCTION_APPROVED',
                        'description' => 'Produksi selesai & disetujui Admin. Siap serah terima ke QC via Surat Jalan.',
                    ]);
                }
                $successCount++;
            } catch (\Exception $e) {
                Log::error("Approve All Production Error (#{$order->id}): " . $e->getMessage());
            }
        }
        
        unset($this->orders);
        $this->dispatch('swal:toast', icon: 'success', title: "$successCount antrean berhasil disetujui");
    }

    protected function autoAssignUnassignedOrders()
    {
        try {
            $techService = app(\App\Services\TechnicianAssignmentService::class);
            $unassignedOrders = WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
                ->whereDoesntHave('logs', function($lq) {
                    $lq->where('step', 'PRODUCTION')
                       ->where('action', 'PRODUCTION_APPROVED');
                })
                ->where(function($q) {
                    $q->whereNull('prod_sol_by')
                      ->orWhereNull('prod_upper_by')
                      ->orWhereNull('prod_cleaning_by');
                })
                ->get();

            foreach ($unassignedOrders as $order) {
                $techService->autoAssignProductionTechnicians($order);
            }
        } catch (\Exception $e) {
            Log::error("Auto assign production error: " . $e->getMessage());
        }
    }

    #[Computed]
    public function orders()
    {
        $this->autoAssignUnassignedOrders();

        $query = WorkOrder::query()
            ->with(['customer', 'workOrderServices', 'prodSolBy', 'prodUpperBy', 'prodCleaningBy', 'cxIssues', 'photos', 'invoice', 'logs', 'revisions']);

        // Search Filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('shoe_brand', 'like', '%' . $this->search . '%');
            });
        }

        // Base Filter: Only show items in PRODUCTION status (that are not yet approved for QC)
        $query->where('status', WorkOrderStatus::PRODUCTION->value)
            ->whereDoesntHave('logs', function($lq) {
                $lq->where('step', 'PRODUCTION')
                   ->where('action', 'PRODUCTION_APPROVED');
            });

        // Tab Filter
        if ($this->activeTab === 'review') {
            $query->productionReview();
        } else {
            // Tab 'reparasi' (Not in review, meaning at least one required stasiun is not completed)
            $query->where(function ($q) {
                $q->whereHas('workOrderServices')
                  ->where(function ($sq) {
                      $sq->where(function ($ssq) {
                          $ssq->whereHas('workOrderServices', function($x) { $x->where('category_name', 'like', '%Sol%'); })
                              ->whereNull('prod_sol_completed_at');
                      })
                      ->orWhere(function ($ssq) {
                          $ssq->whereHas('workOrderServices', function($x) { $x->where('category_name', 'like', '%Upper%'); })
                              ->whereNull('prod_upper_completed_at');
                      })
                      ->orWhere(function ($ssq) {
                          $ssq->whereHas('workOrderServices', function($x) { $x->where('category_name', 'not like', '%Sol%')->where('category_name', 'not like', '%Upper%'); })
                              ->whereNull('prod_cleaning_completed_at');
                      });
                  });
            });
        }

        // Substate Filter for Reparasi Tab
        if ($this->activeTab === 'reparasi' && $this->substate !== 'all') {
            if ($this->substate === 'in_progress') {
                $query->where(function($q) {
                    $q->where(function($sq) {
                        $sq->whereNotNull('prod_sol_started_at')->whereNull('prod_sol_completed_at');
                    })
                    ->orWhere(function($sq) {
                        $sq->whereNotNull('prod_upper_started_at')->whereNull('prod_upper_completed_at');
                    })
                    ->orWhere(function($sq) {
                        $sq->whereNotNull('prod_cleaning_started_at')->whereNull('prod_cleaning_completed_at');
                    });
                });
            } elseif ($this->substate === 'queued') {
                $query->where(function($q) {
                    $q->where(function($sq) {
                        $sq->whereHas('workOrderServices', fn($x) => $x->where('category_name', 'like', '%Sol%'))
                           ->whereNull('prod_sol_started_at')->whereNull('prod_sol_completed_at');
                    })
                    ->orWhere(function($sq) {
                        $sq->whereHas('workOrderServices', fn($x) => $x->where('category_name', 'like', '%Upper%'))
                           ->whereNull('prod_upper_started_at')->whereNull('prod_upper_completed_at');
                    })
                    ->orWhere(function($sq) {
                        $sq->whereHas('workOrderServices', fn($x) => $x->where('category_name', 'not like', '%Sol%')->where('category_name', 'not like', '%Upper%'))
                           ->whereNull('prod_cleaning_started_at')->whereNull('prod_cleaning_completed_at');
                    });
                });
            }
        }

        // Only In Progress Filter
        if ($this->onlyInProgress && $this->activeTab !== 'review') {
            $query->where(function($q) {
                $q->where(function($sq) {
                    $sq->whereNotNull('prod_sol_by')
                       ->whereNotNull('prod_sol_started_at')
                       ->whereNull('prod_sol_completed_at');
                })
                ->orWhere(function($sq) {
                    $sq->whereNotNull('prod_upper_by')
                       ->whereNotNull('prod_upper_started_at')
                       ->whereNull('prod_upper_completed_at');
                })
                ->orWhere(function($sq) {
                    $sq->whereNotNull('prod_cleaning_by')
                       ->whereNotNull('prod_cleaning_started_at')
                       ->whereNull('prod_cleaning_completed_at');
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

        // Technician Filter
        if ($this->technicianFilter !== 'all') {
            $query->where(function($q) {
                $q->where('prod_sol_by', $this->technicianFilter)
                  ->orWhere('prod_upper_by', $this->technicianFilter)
                  ->orWhere('prod_cleaning_by', $this->technicianFilter);
            });
        }

        // Apply Sorting
        $query->orderByRaw("CASE WHEN EXISTS (SELECT 1 FROM cx_issues WHERE cx_issues.work_order_id = work_orders.id AND cx_issues.status = 'RESOLVED') THEN 0 ELSE 1 END");
        $query->orderByRaw("CASE WHEN fast_track_status = 'yes' THEN 0 ELSE 1 END");
        
        if ($this->activeTab !== 'review') {
            $query->orderByRaw("CASE WHEN prod_sol_started_at IS NOT NULL OR prod_upper_started_at IS NOT NULL OR prod_cleaning_started_at IS NOT NULL THEN 0 ELSE 1 END");
        }

        // 2. Then by Priority
        $query->orderByRaw("CASE WHEN priority IN ('Prioritas', 'Urgent', 'Express', 'OTO') THEN 0 ELSE 1 END");

        // 3. Then by custom sort (Latest/Oldest)
        $query->orderBy('id', $this->sort === 'desc' ? 'desc' : 'asc');

        if ($this->activeTab === 'review' && empty($this->search)) {
            $query->productionReview();
        }

        // Reduced per-page to 50 for faster rendering of cards
        return $query->paginate(50);
    }

    public function render()
    {
        return view('livewire.production.station-index', [
            'orders' => $this->orders
        ])->layout('layouts.workshop-pwa');
    }
}
