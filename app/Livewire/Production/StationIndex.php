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
        $excludedSpecs = ['PIC Material Sol', 'PIC Material Upper', 'PIC Material', 'PIC MATERIAL SOL', 'PIC MATERIAL UPPER'];

        $allTechs = User::where('is_active', true)
            ->where('role', 'technician')
            ->whereNotIn('specialization', $excludedSpecs)
            ->where('name', 'not like', '%Dr. Shoe%')
            ->select('id', 'name', 'station', 'specialization')
            ->orderBy('name', 'asc')
            ->get();

        return [
            // Upper: Tim Upper murni (Aji, Dadan, Dede, Dede 2, Dedi, Herman, Jajat, Rian)
            'upper' => $allTechs->filter(fn($u) => ($u->station === 'UPPER' || str_contains(strtolower($u->specialization ?? ''), 'upper')) && !str_contains(strtolower($u->specialization ?? ''), 'material')),
            // Soling: Tim Sol reparasi murni (Agus, Hadi, Ojek, Padon) - tanpa staf preparation (Edi) & tanpa PIC Material
            'sol' => $allTechs->filter(fn($u) => $u->station === 'SOLING' && !str_contains(strtolower($u->specialization ?? ''), 'material')),
            // QC Jahit: Khusus Devi (specialization: QC Jahit)
            'jahit' => $allTechs->filter(fn($u) => str_contains(strtolower($u->specialization ?? ''), 'jahit')),
            'treatment' => $allTechs->filter(fn($u) => $u->station === 'TREATMENT'),
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
            ->whereDoesntHave('otos', fn($q) => $q->whereIn('status', ['ACCEPTED', 'IN_PROGRESS']));

        // SPK yang fisik pengerjaannya selesai dan menunggu review/approval Admin
        // (Belum disetujui Admin dan belum masuk Surat Jalan)
        $reviewQuery = (clone $baseQuery)->productionReview()
            ->whereDoesntHave('logs', fn($lq) => $lq->where('step', 'PRODUCTION')->where('action', 'PRODUCTION_APPROVED'))
            ->whereDoesntHave('suratJalanItems.suratJalan', fn($q) => $q->where('jenis_serah_terima', 'produksi_to_post_qc'));

        $reviewCount = (clone $reviewQuery)->count();

        // SPK antrean pengerjaan fisik aktif (belum selesai & belum disetujui)
        $reparasiQuery = (clone $baseQuery)->where(function ($q) {
            $q->whereDoesntHave('workOrderServices')
              ->orWhereHas('workOrderServices', function ($sq) {
                  $sq->where(function ($ssq) {
                      $ssq->where('category_name', 'like', '%Upper%')
                          ->whereNull('work_orders.prod_upper_completed_at')
                          ->where(fn($uq) => $uq->whereNull('unneeded_stations')->orWhere('unneeded_stations', 'not like', '%"prod_upper"%'));
                  })
                  ->orWhere(function ($ssq) {
                      $ssq->where('category_name', 'like', '%Sol%')
                          ->whereNull('work_orders.prod_sol_completed_at')
                          ->where(fn($uq) => $uq->whereNull('unneeded_stations')->orWhere('unneeded_stations', 'not like', '%"prod_sol"%'));
                  })
                  ->orWhere(function ($ssq) {
                      $ssq->where(function ($x) { $x->where('category_name', 'like', '%Sol%')->orWhere('category_name', 'like', '%Upper%')->orWhere('category_name', 'like', '%Jahit%'); })
                          ->whereNull('work_orders.qc_jahit_completed_at')
                          ->where(fn($uq) => $uq->whereNull('unneeded_stations')->orWhere('unneeded_stations', 'not like', '%"qc_jahit"%'));
                  });
              });
        })->whereDoesntHave('logs', fn($lq) => $lq->where('step', 'PRODUCTION')->where('action', 'PRODUCTION_APPROVED'))
          ->whereDoesntHave('suratJalanItems.suratJalan', fn($q) => $q->where('jenis_serah_terima', 'produksi_to_post_qc'));

        $reparasiCount = (clone $reparasiQuery)->count();

        $inProgressCount = (clone $reparasiQuery)->where(function($q) {
            $q->where(function($sq) {
                $sq->whereNotNull('prod_upper_started_at')->whereNull('prod_upper_completed_at');
            })
            ->orWhere(function($sq) {
                $sq->whereNotNull('prod_sol_started_at')->whereNull('prod_sol_completed_at');
            })
            ->orWhere(function($sq) {
                $sq->whereNotNull('qc_jahit_started_at')->whereNull('qc_jahit_completed_at');
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
            $stationLabel = $this->formatStationName($type);

            if ($techId === 'none') {
                $order->markStationUnneeded($type, Auth::id());
                unset($this->orders);
                $this->dispatch('swal:toast', icon: 'info', title: "Sub-stasiun {$stationLabel} ditandai Tidak Diperlukan");
                return;
            }

            if ($order->isStationUnneeded($type)) {
                $order->restoreStationNeeded($type, $techId ? (int)$techId : null, Auth::id());
                unset($this->orders);
                $techName = $techId ? User::find($techId)?->name : 'Dikosongkan';
                $this->dispatch('swal:toast', icon: 'success', title: "Teknisi {$stationLabel} diaktifkan kembali ke {$techName}");
                return;
            }

            $columnPrefix = $type;
            $oldTechId = $order->{"{$columnPrefix}_by"};
            $oldTechName = $oldTechId ? User::find($oldTechId)?->name : 'Kosong';

            $order->{"{$columnPrefix}_by"} = $techId ? (int)$techId : null;
            $order->save();

            $techName = $techId ? User::find($techId)?->name : 'Dihapus';

            // Audit trail log
            $order->logs()->create([
                'user_id'     => Auth::id(),
                'step'        => 'PRODUCTION',
                'action'      => 'TECHNICIAN_UPDATED',
                'description' => "Teknisi {$stationLabel} diubah dari [{$oldTechName}] ke [{$techName}].",
            ]);

            unset($this->orders);
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
                $q->where(function($sq) {
                    $sq->whereNull('prod_sol_by')
                       ->where(fn($uq) => $uq->whereNull('unneeded_stations')->orWhere('unneeded_stations', 'not like', '%"prod_sol"%'));
                })
                ->orWhere(function($sq) {
                    $sq->whereNull('prod_upper_by')
                       ->where(fn($uq) => $uq->whereNull('unneeded_stations')->orWhere('unneeded_stations', 'not like', '%"prod_upper"%'));
                })
                ->orWhere(function($sq) {
                    $sq->whereNull('qc_jahit_by')
                       ->where(fn($uq) => $uq->whereNull('unneeded_stations')->orWhere('unneeded_stations', 'not like', '%"qc_jahit"%'));
                });
            })
            ->get();

        if ($unassignedOrders->isEmpty()) {
            $this->dispatch('swal:toast', icon: 'info', title: 'Seluruh SPK aktif di Produksi sudah terisi teknisi atau berstatus Tidak Diperlukan.');
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
                    $workflow->advanceStatus(
                        $order,
                        WorkOrderStatus::QC,
                        Auth::id(),
                        'Mass Approved from Production to QC',
                        [
                            'step' => 'PRODUCTION',
                            'action' => 'PRODUCTION_APPROVED'
                        ]
                    );
                }
                $successCount++;
            } catch (\Exception $e) {
                Log::error("Mass Approve Error (#{$order->id}): " . $e->getMessage());
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
                ->where(function($q) {
                    $q->whereNull('prod_upper_by')
                      ->orWhereNull('prod_sol_by')
                      ->orWhereNull('qc_jahit_by');
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
            ->with(['customer', 'workOrderServices', 'prodUpperBy', 'prodSolBy', 'qcJahitBy', 'cxIssues', 'photos', 'invoice', 'logs', 'revisions', 'suratJalanItems.suratJalan']);

        // Base Filter: Only show items in PRODUCTION status (excluding active OTOs which are handled in Stasiun OTO)
        $query->where('status', WorkOrderStatus::PRODUCTION->value)
            ->whereDoesntHave('otos', fn($q) => $q->whereIn('status', ['ACCEPTED', 'IN_PROGRESS']));

        // Universal Search Filter (Searches across all production orders when query is given)
        if (!empty(trim($this->search))) {
            $searchTerm = trim($this->search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('spk_number', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('shoe_brand', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_phone', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('customer', function($cq) use ($searchTerm) {
                      $cq->where('name', 'like', '%' . $searchTerm . '%')
                         ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                  });
            });
        } else {
            // Tab Filter
            if ($this->activeTab === 'review') {
                $query->productionReview()
                    ->whereDoesntHave('logs', fn($lq) => $lq->where('step', 'PRODUCTION')->where('action', 'PRODUCTION_APPROVED'))
                    ->whereDoesntHave('suratJalanItems.suratJalan', fn($q) => $q->where('jenis_serah_terima', 'produksi_to_post_qc'));
            } else {
                // Tab 'reparasi' (Not in review, meaning still needs processing)
                $query->where(function ($q) {
                    $q->whereDoesntHave('workOrderServices')
                      ->orWhereHas('workOrderServices', function ($sq) {
                          $sq->where(function ($ssq) {
                              $ssq->where('category_name', 'like', '%Upper%')
                                  ->whereNull('work_orders.prod_upper_completed_at');
                          })
                          ->orWhere(function ($ssq) {
                              $ssq->where('category_name', 'like', '%Sol%')
                                  ->whereNull('work_orders.prod_sol_completed_at');
                          })
                          ->orWhere(function ($ssq) {
                              $ssq->where(function ($x) { $x->where('category_name', 'like', '%Sol%')->orWhere('category_name', 'like', '%Upper%')->orWhere('category_name', 'like', '%Jahit%'); })
                                  ->whereNull('work_orders.qc_jahit_completed_at');
                          });
                      });
                })->whereDoesntHave('logs', fn($lq) => $lq->where('step', 'PRODUCTION')->where('action', 'PRODUCTION_APPROVED'))
                  ->whereDoesntHave('suratJalanItems.suratJalan', fn($q) => $q->where('jenis_serah_terima', 'produksi_to_post_qc'));
            }

            // Substate Filter for Reparasi Tab
            if ($this->activeTab === 'reparasi' && $this->substate !== 'all') {
                if ($this->substate === 'in_progress') {
                    $query->where(function($q) {
                        $q->where(function($sq) {
                            $sq->whereNotNull('prod_upper_started_at')->whereNull('prod_upper_completed_at');
                        })
                        ->orWhere(function($sq) {
                            $sq->whereNotNull('prod_sol_started_at')->whereNull('prod_sol_completed_at');
                        })
                        ->orWhere(function($sq) {
                            $sq->whereNotNull('qc_jahit_started_at')->whereNull('qc_jahit_completed_at');
                        });
                    });
                } elseif ($this->substate === 'queued') {
                    $query->where(function($q) {
                        $q->whereNull('prod_upper_started_at')->whereNull('prod_upper_completed_at')
                           ->whereNull('prod_sol_started_at')->whereNull('prod_sol_completed_at')
                           ->whereNull('qc_jahit_started_at')->whereNull('qc_jahit_completed_at');
                    });
                }
            }
        }

        // Only In Progress Filter
        if ($this->onlyInProgress && $this->activeTab !== 'review') {
            $query->where(function($q) {
                $q->where(function($sq) {
                    $sq->whereNotNull('prod_upper_by')
                       ->whereNotNull('prod_upper_started_at')
                       ->whereNull('prod_upper_completed_at');
                })
                ->orWhere(function($sq) {
                    $sq->whereNotNull('prod_sol_by')
                       ->whereNotNull('prod_sol_started_at')
                       ->whereNull('prod_sol_completed_at');
                })
                ->orWhere(function($sq) {
                    $sq->whereNotNull('qc_jahit_by')
                       ->whereNotNull('qc_jahit_started_at')
                       ->whereNull('qc_jahit_completed_at');
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
                $q->where('prod_upper_by', $this->technicianFilter)
                  ->orWhere('prod_sol_by', $this->technicianFilter)
                  ->orWhere('qc_jahit_by', $this->technicianFilter);
            });
        }

        // Apply Sorting
        $query->orderByRaw("CASE WHEN EXISTS (SELECT 1 FROM cx_issues WHERE cx_issues.work_order_id = work_orders.id AND cx_issues.status = 'RESOLVED') THEN 0 ELSE 1 END");
        $query->orderByRaw("CASE WHEN fast_track_status = 'yes' THEN 0 ELSE 1 END");
        
        if ($this->activeTab !== 'review') {
            $query->orderByRaw("CASE WHEN prod_upper_started_at IS NOT NULL OR prod_sol_started_at IS NOT NULL OR qc_jahit_started_at IS NOT NULL THEN 0 ELSE 1 END");
        }

        // 2. Then by Priority
        $query->orderByRaw("CASE WHEN priority IN ('Prioritas', 'Urgent', 'Express', 'OTO') THEN 0 ELSE 1 END");

        // 3. Then by custom sort (Latest/Oldest)
        $query->orderBy('id', $this->sort === 'desc' ? 'desc' : 'asc');

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
