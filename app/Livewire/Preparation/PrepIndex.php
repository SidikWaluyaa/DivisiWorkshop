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
        $allActive = User::where('is_active', true)->whereIn('role', ['technician', 'admin'])->select('id', 'name')->get();

        $washing = User::where('is_active', true)
            ->where(function($q) {
                $q->whereIn('specialization', ['Washing', 'Persiapan (Cuci)', 'Treatment', 'Clean Up'])
                  ->orWhere('station', 'PREPARATION');
            })->select('id', 'name')->get();
        if ($washing->isEmpty()) $washing = $allActive;

        $sol = User::where('is_active', true)
            ->where(function($q) {
                $q->whereIn('specialization', ['Bongkar Sol', 'Prep Sol', 'Sol Repair', 'PIC Material Sol'])
                  ->orWhere('station', 'PREPARATION')
                  ->orWhere('station', 'SOLING');
            })->select('id', 'name')->get();
        if ($sol->isEmpty()) $sol = $allActive;

        $upper = User::where('is_active', true)
            ->where(function($q) {
                $q->whereIn('specialization', ['Bongkar Upper', 'Prep Upper', 'Upper Repair', 'Repaint', 'Jahit', 'PIC Material Upper'])
                  ->orWhere('station', 'PREPARATION')
                  ->orWhere('station', 'UPPER');
            })->select('id', 'name')->get();
        if ($upper->isEmpty()) $upper = $allActive;

        return [
            'washing' => $washing,
            'sol' => $sol,
            'upper' => $upper,
            'review' => User::where('role', 'admin')->select('id', 'name')->get(),
        ];
    }

    #[Computed]
    public function counts()
    {
        $baseQuery = WorkOrder::where('status', WorkOrderStatus::PREPARATION);

        // PREP stage: 3 Sub-Tabs
        $queueCount = (clone $baseQuery)->whereNull('prep_washing_started_at')->count();
        $inProgressCount = (clone $baseQuery)->whereNotNull('prep_washing_started_at')->whereNull('prep_washing_completed_at')->count();
        $reviewCount = (clone $baseQuery)->whereNotNull('prep_washing_completed_at')->count();

        return [
            'queue' => $queueCount,
            'in_progress' => $inProgressCount,
            'review' => $reviewCount,
        ];
    }

    /**
     * Balanced Round-Robin Auto-Assign Technicians to WorkOrders (Cuci, Sol Prep, Upper Prep)
     */
    private function distributeTechnicians($orders)
    {
        $allActiveTechs = User::where('is_active', true)->whereIn('role', ['technician', 'admin'])->get();

        $washingTechs = User::where('is_active', true)->where(function($q) {
            $q->whereIn('specialization', ['Washing', 'Persiapan (Cuci)', 'Treatment', 'Clean Up'])->orWhere('station', 'PREPARATION');
        })->get();
        if ($washingTechs->isEmpty()) $washingTechs = $allActiveTechs;

        $solTechs = User::where('is_active', true)->where(function($q) {
            $q->whereIn('specialization', ['Bongkar Sol', 'Prep Sol', 'Sol Repair', 'PIC Material Sol'])->orWhere('station', 'PREPARATION')->orWhere('station', 'SOLING');
        })->get();
        if ($solTechs->isEmpty()) $solTechs = $allActiveTechs;

        $upperTechs = User::where('is_active', true)->where(function($q) {
            $q->whereIn('specialization', ['Bongkar Upper', 'Prep Upper', 'Upper Repair', 'Repaint', 'Jahit', 'PIC Material Upper'])->orWhere('station', 'PREPARATION')->orWhere('station', 'UPPER');
        })->get();
        if ($upperTechs->isEmpty()) $upperTechs = $allActiveTechs;

        $assignedCount = 0;

        foreach ($orders as $order) {
            $updated = false;

            // 1. Auto Assign Washing if unassigned
            if (!$order->prep_washing_by && $washingTechs->isNotEmpty()) {
                $order->prep_washing_by = $washingTechs->random()->id;
                $updated = true;
            }

            // 2. Auto Assign Sol Prep if required & unassigned
            if ($order->needs_prep_sol && !$order->prep_sol_by && $solTechs->isNotEmpty()) {
                $order->prep_sol_by = $solTechs->random()->id;
                $updated = true;
            }

            // 3. Auto Assign Upper Prep if required & unassigned
            if ($order->needs_prep_upper && !$order->prep_upper_by && $upperTechs->isNotEmpty()) {
                $order->prep_upper_by = $upperTechs->random()->id;
                $updated = true;
            }

            if ($updated) {
                $order->save();
                $assignedCount++;

                \App\Models\WorkOrderLog::create([
                    'work_order_id' => $order->id,
                    'user_id' => Auth::id() ?? 1,
                    'action' => 'prep_auto_assign',
                    'description' => 'Auto-assign teknisi Preparation (Cuci / Sol / Upper)',
                    'step' => WorkOrderStatus::PREPARATION->value
                ]);
            }
        }

        return $assignedCount;
    }

    public function startPrepWashing($id)
    {
        $order = WorkOrder::find($id);
        if (!$order) return;

        try {
            $now = \Illuminate\Support\Carbon::now();
            $authId = Auth::id() ?? 1;

            if (!$order->prep_washing_started_at) {
                if (!$order->prep_washing_by) {
                    $this->distributeTechnicians([$order]);
                }

                $order->prep_washing_started_at = $now;
                $order->prep_washing_by = $order->prep_washing_by ?? $authId;
                
                \App\Models\WorkOrderLog::create([
                    'work_order_id' => $order->id,
                    'user_id' => $order->prep_washing_by,
                    'action' => 'prep_washing_start',
                    'description' => 'Mulai pengerjaan Prep Washing',
                    'step' => WorkOrderStatus::PREPARATION->value
                ]);
                $order->save();
            }

            unset($this->orders);
            $this->dispatch('swal:toast', icon: 'success', title: 'Pengerjaan Cuci dimulai!');
        } catch (\Throwable $e) {
            Log::error('Start Prep Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function autoAssignManifestPrep($manifestId)
    {
        try {
            $ordersQuery = WorkOrder::where('status', WorkOrderStatus::PREPARATION);
            if ($manifestId === 'orphan') {
                $ordersQuery->whereNull('workshop_manifest_id');
            } else {
                $ordersQuery->where('workshop_manifest_id', $manifestId);
            }

            $allOrders = $ordersQuery->get();
            $ordersToAssign = $allOrders->filter(function($o) {
                return !$o->prep_washing_by || ($o->needs_prep_sol && !$o->prep_sol_by) || ($o->needs_prep_upper && !$o->prep_upper_by);
            });

            if ($ordersToAssign->isEmpty()) {
                $this->dispatch('swal:toast', icon: 'info', title: 'Seluruh SPK & sub-tugas di Manifest ini sudah memiliki teknisi!');
                return;
            }

            $count = $this->distributeTechnicians($ordersToAssign);
            unset($this->orders);
            $this->dispatch('swal:toast', icon: 'success', title: "$count SPK berhasil ditugaskan teknisi (Auto-Assign Preparation)!");
        } catch (\Throwable $e) {
            Log::error('Auto Assign Manifest Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function autoAssignSelectedPrep()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih SPK terlebih dahulu');
            return;
        }

        try {
            $orders = WorkOrder::whereIn('id', $this->selectedItems)
                ->where('status', WorkOrderStatus::PREPARATION)
                ->get();

            $count = $this->distributeTechnicians($orders);
            $this->selectedItems = [];
            $this->selectAll = false;
            unset($this->orders);

            $this->dispatch('swal:toast', icon: 'success', title: "$count SPK berhasil ditugaskan teknisi secara otomatis!");
        } catch (\Throwable $e) {
            Log::error('Auto Assign Selected Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function startManifestPrep($manifestId)
    {
        try {
            $now = \Illuminate\Support\Carbon::now();
            $authId = Auth::id() ?? 1;

            $ordersQuery = WorkOrder::where('status', WorkOrderStatus::PREPARATION)
                ->whereNull('prep_washing_started_at');

            if ($manifestId === 'orphan') {
                $ordersQuery->whereNull('workshop_manifest_id');
            } else {
                $ordersQuery->where('workshop_manifest_id', $manifestId);
            }

            $orders = $ordersQuery->get();

            // Auto-assign any unassigned SPKs using Balanced Round-Robin algorithm
            $unassignedOrders = $orders->filter(function($o) {
                return !$o->prep_washing_by || ($o->needs_prep_sol && !$o->prep_sol_by) || ($o->needs_prep_upper && !$o->prep_upper_by);
            });
            if ($unassignedOrders->isNotEmpty()) {
                $this->distributeTechnicians($unassignedOrders);
            }

            $count = 0;
            foreach ($orders->fresh() as $order) {
                $order->prep_washing_started_at = $now;
                $order->prep_washing_by = $order->prep_washing_by ?? $authId;
                $order->save();

                \App\Models\WorkOrderLog::create([
                    'work_order_id' => $order->id,
                    'user_id' => $order->prep_washing_by,
                    'action' => 'prep_washing_start',
                    'description' => 'Mulai pengerjaan Prep Washing (Batch Manifest)',
                    'step' => WorkOrderStatus::PREPARATION->value
                ]);
                $count++;
            }

            unset($this->orders);
            $this->dispatch('swal:toast', icon: 'success', title: "$count SPK di Manifest berhasil dimulai & teknisi di-assign!");
        } catch (\Throwable $e) {
            Log::error('Start Manifest Prep Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function completeManifestPrep($manifestId)
    {
        try {
            $now = \Illuminate\Support\Carbon::now();
            $authId = Auth::id() ?? 1;

            $ordersQuery = WorkOrder::where('status', WorkOrderStatus::PREPARATION)
                ->whereNotNull('prep_washing_started_at')
                ->whereNull('prep_washing_completed_at');

            if ($manifestId === 'orphan') {
                $ordersQuery->whereNull('workshop_manifest_id');
            } else {
                $ordersQuery->where('workshop_manifest_id', $manifestId);
            }

            $orders = $ordersQuery->get();
            $count = 0;

            foreach ($orders as $order) {
                $order->prep_washing_completed_at = $now;
                $order->save();

                \App\Models\WorkOrderLog::create([
                    'work_order_id' => $order->id,
                    'user_id' => $order->prep_washing_by ?? $authId,
                    'action' => 'prep_washing_finish',
                    'description' => 'Selesai pengerjaan Prep Washing (Batch Manifest)',
                    'step' => WorkOrderStatus::PREPARATION->value
                ]);
                $count++;
            }

            unset($this->orders);
            $this->dispatch('swal:toast', icon: 'success', title: "$count SPK di Manifest diselesaikan ke Review Admin!");
        } catch (\Throwable $e) {
            Log::error('Complete Manifest Prep Error: ' . $e->getMessage());
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
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
                    'description' => 'Menyelesaikan proses Prep Washing',
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
                        'description' => 'Menyelesaikan proses Prep Washing (Bulk)',
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

                    // Washing Prep
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

        // 3 Sub-Tabs Filter for Preparation
        if ($this->activeTab === 'queue') {
            $woQuery->whereNull('prep_washing_started_at');
        } elseif ($this->activeTab === 'in_progress') {
            $woQuery->whereNotNull('prep_washing_started_at')
                    ->whereNull('prep_washing_completed_at');
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
        ])->layout('layouts.workshop-pwa');
    }
}
