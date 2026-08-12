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

    #[Url(except: 'queue')]
    public $activeTab = 'queue';

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
    public $expandedManifests = [];

    public function toggleManifest($manifestId)
    {
        $idStr = (string)$manifestId;
        if (in_array($idStr, $this->expandedManifests)) {
            $this->expandedManifests = array_diff($this->expandedManifests, [$idStr]);
        } else {
            $this->expandedManifests[] = $idStr;
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $ids = [];
            foreach ($this->orders()->items() as $group) {
                foreach ($group->work_orders as $wo) {
                    $ids[] = (string)$wo->id;
                }
            }
            $this->selectedItems = $ids;
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        $totalOrdersOnPage = 0;
        foreach ($this->orders()->items() as $group) {
            $totalOrdersOnPage += $group->work_orders->count();
        }
        $this->selectAll = $totalOrdersOnPage > 0 && count(array_intersect($this->selectedItems, $this->getAllPageOrderIds())) === $totalOrdersOnPage;
    }

    private function getAllPageOrderIds()
    {
        $ids = [];
        foreach ($this->orders()->items() as $group) {
            foreach ($group->work_orders as $wo) {
                $ids[] = (string)$wo->id;
            }
        }
        return $ids;
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPriority() { $this->resetPage(); }
    public function updatingTechnicianFilter() { $this->resetPage(); }
    public function updatingOnlyInProgress() { $this->resetPage(); }
    public function updatingActiveTab() { $this->resetPage(); $this->selectedItems = []; $this->onlyInProgress = false; $this->expandedManifests = []; }

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

        // PREP stage only tracks Washing (Cuci). Bongkar Sol/Upper moved to Sortir.
        $queueCount = (clone $baseQuery)->whereNull('prep_washing_completed_at')->count();

        $reviewCount = (clone $baseQuery)->whereNotNull('prep_washing_completed_at')->count();

        return [
            'queue' => $queueCount,
            'review' => $reviewCount,
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

    public function completeAllPrep($id)
    {
        $order = WorkOrder::find($id);
        if (!$order) return;

        try {
            $now = \Illuminate\Support\Carbon::now();
            $authId = Auth::id() ?? 1;

            // PREP stage: Only Washing (Cuci). Bongkar Sol/Upper handled in Sortir.
            if (!$order->prep_washing_completed_at) {
                if (!$order->prep_washing_by) {
                    $order->prep_washing_by = $authId;
                }
                if (!$order->prep_washing_started_at) {
                    $order->prep_washing_started_at = $now;
                }
                $order->prep_washing_completed_at = $now;
                \App\Models\WorkOrderLog::create([
                    'work_order_id' => $order->id,
                    'user_id' => $order->prep_washing_by,
                    'action' => 'prep_washing_finish',
                    'description' => 'Menyelesaikan proses Prep Washing (Selesaikan Semua)',
                    'step' => WorkOrderStatus::PREPARATION->value
                ]);
            }

            $order->save();
            unset($this->orders); // clear livewire cache
            $this->dispatch('swal:toast', icon: 'success', title: 'Proses Cuci berhasil diselesaikan!');
        } catch (\Throwable $e) {
            Log::error('Complete All Prep Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function bulkCompleteAllPrep()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih item terlebih dahulu');
            return;
        }

        $successCount = 0;
        $now = \Illuminate\Support\Carbon::now();
        $authId = Auth::id() ?? 1;

        foreach ($this->selectedItems as $id) {
            try {
                $order = WorkOrder::find($id);
                if (!$order) continue;

                // PREP stage: Only Washing (Cuci)
                if (!$order->prep_washing_completed_at) {
                    if (!$order->prep_washing_by) {
                        $order->prep_washing_by = $authId;
                    }
                    if (!$order->prep_washing_started_at) {
                        $order->prep_washing_started_at = $now;
                    }
                    $order->prep_washing_completed_at = $now;
                    \App\Models\WorkOrderLog::create([
                        'work_order_id' => $order->id,
                        'user_id' => $order->prep_washing_by,
                        'action' => 'prep_washing_finish',
                        'description' => 'Menyelesaikan proses Prep Washing (Bulk Selesaikan Semua)',
                        'step' => WorkOrderStatus::PREPARATION->value
                    ]);
                }

                $order->save();
                $successCount++;
            } catch (\Exception $e) {
                Log::error("Bulk Complete All Prep Error (#$id): " . $e->getMessage());
            }
        }

        $this->selectedItems = [];
        $this->selectAll = false;
        unset($this->orders); // clear livewire cache
        $this->dispatch('swal:toast', icon: 'success', title: "$successCount SPK berhasil diselesaikan Cuci secara massal!");
    }

    public function bulkAction($action, $techs = [])
    {
        $workflow = app(WorkflowService::class);
        if (empty($this->selectedItems)) {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih item terlebih dahulu');
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
                    $updated = false;

                    // 1. Washing Prep
                    $washingTechId = $techs['washing'] ?? null;
                    if ($washingTechId) {
                        $this->handleStationUpdate(
                            $order, 
                            'prep_washing', 
                            $action === 'assign' ? 'start' : $action, 
                            Auth::id(), 
                            $washingTechId, 
                            WorkOrderStatus::PREPARATION->value
                        );
                        $updated = true;
                    }

                    // 2. Sol Prep
                    $solTechId = $techs['sol'] ?? null;
                    if ($solTechId && $order->needs_prep_sol) {
                        $this->handleStationUpdate(
                            $order, 
                            'prep_sol', 
                            $action === 'assign' ? 'start' : $action, 
                            Auth::id(), 
                            $solTechId, 
                            WorkOrderStatus::PREPARATION->value
                        );
                        $updated = true;
                    }

                    // 3. Upper Prep
                    $upperTechId = $techs['upper'] ?? null;
                    if ($upperTechId && $order->needs_prep_upper) {
                        $this->handleStationUpdate(
                            $order, 
                            'prep_upper', 
                            $action === 'assign' ? 'start' : $action, 
                            Auth::id(), 
                            $upperTechId, 
                            WorkOrderStatus::PREPARATION->value
                        );
                        $updated = true;
                    }

                    if ($updated) {
                        $order->save();
                        $successCount++;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Bulk Preparation Error (#$id): " . $e->getMessage());
            }
        }

        $this->selectedItems = [];
        $this->selectAll = false;
        unset($this->orders);
        
        $msg = $action === 'approve' 
            ? "$successCount SPK berhasil diapprove ke Sortir!" 
            : ($action === 'assign' ? "$successCount SPK berhasil ditugaskan teknisi!" : "$successCount SPK berhasil diselesaikan tugas persiapannya!");
            
        $this->dispatch('swal:toast', icon: 'success', title: $msg);
    }

    public function performApprove($id)
    {
        $workflow = app(WorkflowService::class);
        $order = WorkOrder::find($id);
        if ($order) {
            try {
                $this->performApproveLogic($order, $workflow);
                unset($this->orders);
                $this->dispatch('swal:toast', icon: 'success', title: 'Berhasil disetujui!');
            } catch (\Exception $e) {
                $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
            }
        }
    }

    public function approveAll()
    {
        $workflow = app(\App\Services\WorkflowService::class);
        $groupsToApprove = $this->orders->items();
        
        $successCount = 0;
        foreach ($groupsToApprove as $group) {
            foreach ($group->work_orders as $order) {
                try {
                    $this->performApproveLogic($order, $workflow);
                    $successCount++;
                } catch (\Exception $e) {
                    Log::error("Approve All Prep Error (#{$order->id}): " . $e->getMessage());
                }
            }
        }
        
        unset($this->orders);
        $this->dispatch('swal:toast', icon: 'success', title: "$successCount antrean berhasil disetujui");
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

        // Auto-complete any unfinished preparation tasks on Admin Approval
        if (!$order->is_ready) {
            $now = \Illuminate\Support\Carbon::now();
            $authId = Auth::id() ?? 1;

            if (!$order->prep_washing_completed_at) {
                if (!$order->prep_washing_by) {
                    $order->prep_washing_by = $authId;
                }
                if (!$order->prep_washing_started_at) {
                    $order->prep_washing_started_at = $now;
                }
                $order->prep_washing_completed_at = $now;
                \App\Models\WorkOrderLog::create([
                    'work_order_id' => $order->id,
                    'user_id' => $order->prep_washing_by,
                    'action' => 'prep_washing_finish',
                    'description' => 'Menyelesaikan proses Prep Washing (Otomatis saat Approval Admin)',
                    'step' => WorkOrderStatus::PREPARATION->value
                ]);
            }

            $order->save();
        }

        $workflow->updateStatus($order, WorkOrderStatus::SORTIR, 'Preparation Approved by Admin. Proceed to Sortir.');
    }

    #[Computed]
    public function orders()
    {
        $woQuery = WorkOrder::query()
            ->where('status', WorkOrderStatus::PREPARATION);

        // Search Filter
        if ($this->search) {
            $woQuery->where(function($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('shoe_brand', 'like', '%' . $this->search . '%');
            });
        }

        // Tab Filter — PREP stage only tracks Washing (Cuci)
        if ($this->activeTab === 'queue') {
            $woQuery->whereNull('prep_washing_completed_at');
        } elseif ($this->activeTab === 'review') {
            $woQuery->whereNotNull('prep_washing_completed_at');
        }

        // Only In Progress Filter — PREP only tracks Washing
        if ($this->onlyInProgress && $this->activeTab === 'queue') {
            $woQuery->whereNotNull('prep_washing_by')
                    ->whereNotNull('prep_washing_started_at')
                    ->whereNull('prep_washing_completed_at');
        }

        // Priority Filter
        if ($this->priority !== 'all') {
            if ($this->priority === 'urgent') {
                $woQuery->whereIn('priority', ['Prioritas', 'Urgent', 'Express', 'OTO']);
            } else {
                $woQuery->where('priority', 'Regular');
            }
        }

        // Technician Filter — PREP only tracks Washing
        if ($this->technicianFilter !== 'all' && $this->activeTab === 'queue') {
            $woQuery->where('prep_washing_by', $this->technicianFilter);
        }

        // 1. Determine which Manifest IDs contain active SPKs matching the filters
        $hasOrphan = (clone $woQuery)->whereNull('workshop_manifest_id')->exists();
        $manifestIds = (clone $woQuery)->whereNotNull('workshop_manifest_id')
            ->pluck('workshop_manifest_id')
            ->unique()
            ->toArray();

        // 2. Combine Group Keys (Orphan + Manifest IDs)
        $allGroupKeys = [];
        if ($hasOrphan) {
            $allGroupKeys[] = 'orphan';
        }
        
        $sortedManifestIds = \App\Models\WorkshopManifest::whereIn('id', $manifestIds)
            ->orderBy('id', $this->sort === 'desc' ? 'desc' : 'asc')
            ->pluck('id')
            ->toArray();
            
        $allGroupKeys = array_merge($allGroupKeys, $sortedManifestIds);

        // 3. Paginate the Group Keys
        $currentPage = $this->paginators['page'] ?? 1;
        $perPage = 10;
        $total = count($allGroupKeys);
        $slice = array_slice($allGroupKeys, ($currentPage - 1) * $perPage, $perPage);

        // 4. Retrieve models for this page slice
        $pageManifestIds = array_filter($slice, fn($v) => $v !== 'orphan');
        $includeOrphan = in_array('orphan', $slice);

        $manifests = \App\Models\WorkshopManifest::whereIn('id', $pageManifestIds)
            ->with(['dispatcher', 'receiver'])
            ->get()
            ->keyBy('id');

        // Fetch SPK orders matching page slice
        $pageOrders = (clone $woQuery)
            ->with(['customer', 'workOrderServices', 'prepWashingBy', 'prepSolBy', 'prepUpperBy', 'cxIssues', 'photos', 'invoice'])
            ->where(function($q) use ($pageManifestIds, $includeOrphan) {
                if (!empty($pageManifestIds)) {
                    $q->whereIn('workshop_manifest_id', $pageManifestIds);
                }
                if ($includeOrphan) {
                    $q->orWhereNull('workshop_manifest_id');
                }
            })
            ->orderByRaw("CASE WHEN EXISTS (SELECT 1 FROM cx_issues WHERE cx_issues.work_order_id = work_orders.id AND cx_issues.status = 'RESOLVED') THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN fast_track_status = 'yes' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN priority IN ('Prioritas', 'Urgent', 'Express', 'OTO') THEN 0 ELSE 1 END")
            ->orderBy('id', $this->sort === 'desc' ? 'desc' : 'asc')
            ->get();

        $groupedOrders = $pageOrders->groupBy(function($order) {
            return $order->workshop_manifest_id ?: 'orphan';
        });

        // 5. Structure the final paginated results
        $items = [];
        foreach ($slice as $key) {
            if ($key === 'orphan') {
                $items[] = (object)[
                    'id' => 'orphan',
                    'is_orphan' => true,
                    'manifest_number' => 'Unit Tanpa Manifest (Mandiri)',
                    'notes' => 'SPK yang diproses langsung tanpa manifest logistik.',
                    'created_at' => null,
                    'work_orders' => $groupedOrders->get('orphan', collect())
                ];
            } else {
                $manifest = $manifests->get($key);
                if ($manifest) {
                    $items[] = (object)[
                        'id' => $key,
                        'is_orphan' => false,
                        'manifest_number' => $manifest->manifest_number,
                        'notes' => $manifest->notes,
                        'created_at' => $manifest->created_at,
                        'work_orders' => $groupedOrders->get($key, collect())
                    ];
                }
            }
        }

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items, 
            $total, 
            $perPage, 
            $currentPage, 
            [
                'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
                'pageName' => 'page'
            ]
        );
    }

    public function render()
    {
        return view('livewire.preparation.prep-index', [
            'orders' => $this->orders
        ])->layout('layouts.app');
    }
}
