<?php

namespace App\Livewire\Sortir;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use App\Models\Material;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use App\Services\MaterialManagementService;
use App\Models\StorageRack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'ready';
    public $selectedItems = [];

    // FollowUp / Lapor Kendala Modal Properties (FR-9.4)
    public $showFollowUpModal = false;
    public $selectedOrderId = null;
    public $kategoriMasalah = 'Teknis';
    public $detailKendala1 = '';
    public $detailKendala2 = '';
    public $opsiSolusi1 = '';
    public $opsiSolusi2 = '';
    public $estimasiWaktuTambahan = '';
    public $catatanKendala = '';

    // Pengajuan Belanja Modal Properties
    public $showPengajuanModal = false;
    public $pengajuanOrderId = null;
    public $pengajuanSpkNumber = '';
    public $pengajuanMaterials = [];
    public $pengajuanNotes = '';
    public $selectedWaitingItems = [];
    public $isBulkPengajuan = false;

    // Filter & Sort Properties
    public $sortBy = 'priority_newest';
    public $filterPriority = '';
    public $filterBrand = '';
    public $filterType = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'ready'],
        'sortBy' => ['except' => 'priority_newest'],
        'filterPriority' => ['except' => ''],
        'filterBrand' => ['except' => ''],
        'filterType' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage('readyPage');
        $this->resetPage('waitingPage');
        $this->resetPage('needsPage');
    }

    public function resetFilters()
    {
        $this->reset(['filterPriority', 'filterBrand', 'filterType', 'search']);
        $this->sortBy = 'priority_newest';
    }

    public function toggleGroup($ids)
    {
        $ids = array_map('strval', $ids);
        $allSelected = collect($ids)->every(fn($id) => in_array($id, $this->selectedItems));

        if ($allSelected) {
            $this->selectedItems = array_diff($this->selectedItems, $ids);
        } else {
            foreach ($ids as $id) {
                if (!in_array($id, $this->selectedItems)) {
                    $this->selectedItems[] = $id;
                }
            }
        }
    }

    public function bulkSkipToProduction()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih order terlebih dahulu.']);
            return;
        }

        if (!in_array(Auth::user()->role, ['admin', 'owner', 'production_manager'])) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Hanya Admin/Manager yang bisa bypass.']);
            return;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($this->selectedItems as $id) {
            try {
                $order = WorkOrder::findOrFail($id);
                $oldStatus = $order->status;

                DB::transaction(function () use ($order, $oldStatus) {
                    $order->status = WorkOrderStatus::PRODUCTION;
                    $order->current_location = 'Rumah Abu';
                    $order->save();

                    \App\Events\WorkOrderStatusUpdated::dispatch(
                        $order, 
                        $oldStatus, 
                        WorkOrderStatus::PRODUCTION, 
                        'Bulk Bypass (Livewire)', 
                        Auth::id()
                    );
                });
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
            }
        }

        $this->selectedItems = [];
        $this->dispatch('notify', ['type' => 'success', 'message' => "Bulk Bypass selesai. Berhasil: $successCount, Gagal: $failCount"]);
    }

    public function bypassSingle($id)
    {
        if (!in_array(Auth::user()->role, ['admin', 'owner', 'production_manager'])) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Hanya Admin/Manager yang bisa bypass.']);
            return;
        }

        try {
            $order = WorkOrder::findOrFail($id);
            $oldStatus = $order->status;

            DB::transaction(function () use ($order, $oldStatus) {
                $order->status = WorkOrderStatus::PRODUCTION;
                $order->current_location = 'Rumah Abu';
                $order->save();

                \App\Events\WorkOrderStatusUpdated::dispatch(
                    $order, 
                    $oldStatus, 
                    WorkOrderStatus::PRODUCTION, 
                    'Single Bypass (Sortir Index)', 
                    Auth::id()
                );
            });

            $this->dispatch('notify', ['type' => 'success', 'message' => "Order #{$order->spk_number} berhasil dikirim ke Production."]);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal bypass: ' . $e->getMessage()]);
        }
    }

    public function requestMaterial(MaterialManagementService $service, $id)
    {
        try {
            $order = WorkOrder::findOrFail($id);
            $request = $service->requestMissingMaterialsForWorkOrder($order);

            if ($request) {
                $this->dispatch('notify', [
                    'type' => 'success', 
                    'message' => "Request #{$request->request_number} berhasil dibuat & dikirim ke Purchasing."
                ]);
            } else {
                $this->dispatch('notify', ['type' => 'info', 'message' => "Tidak ada material yang perlu direquest."]);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => "Gagal: " . $e->getMessage()]);
        }
    }

    public function openFollowUpModal($id)
    {
        $this->selectedOrderId = $id;
        $this->kategoriMasalah = 'Teknis';
        $this->detailKendala1 = '';
        $this->detailKendala2 = '';
        $this->opsiSolusi1 = '';
        $this->opsiSolusi2 = '';
        $this->estimasiWaktuTambahan = '';
        $this->catatanKendala = '';
        $this->showFollowUpModal = true;
    }

    // ═══ Pengajuan Belanja Modal Methods ═══

    public function toggleSelectWaitingAll($ids = [])
    {
        $ids = array_map('strval', (array)$ids);
        $allSelected = count($ids) > 0 && collect($ids)->every(fn($id) => in_array($id, $this->selectedWaitingItems));

        if ($allSelected) {
            $this->selectedWaitingItems = array_values(array_diff($this->selectedWaitingItems, $ids));
        } else {
            foreach ($ids as $id) {
                if (!in_array($id, $this->selectedWaitingItems)) {
                    $this->selectedWaitingItems[] = (string)$id;
                }
            }
        }
    }

    public function openBulkPengajuanModal()
    {
        if (empty($this->selectedWaitingItems)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih setidaknya 1 SPK dari antrean waiting.']);
            return;
        }

        $orders = WorkOrder::whereIn('id', $this->selectedWaitingItems)
            ->with(['materials' => function($q) {
                $q->where('work_order_materials.status', '!=', 'RECEIVED');
            }])->get();

        $allMaterials = [];
        foreach ($orders as $order) {
            foreach ($order->materials as $mat) {
                $allMaterials[] = [
                    'work_order_id'   => $order->id,
                    'spk_number'      => $order->spk_number,
                    'customer_name'   => $order->customer_name,
                    'material_id'     => $mat->id,
                    'name'            => $mat->name,
                    'sub_category'    => $mat->sub_category ?? '-',
                    'unit'            => $mat->unit ?? 'pcs',
                    'quantity'        => $mat->pivot->quantity,
                    'price'           => $mat->price ?? 0,
                ];
            }
        }

        if (empty($allMaterials)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Tidak ada material belanja pada SPK yang dipilih.']);
            return;
        }

        $this->isBulkPengajuan = true;
        $this->pengajuanOrderId = null;
        $this->pengajuanSpkNumber = count($this->selectedWaitingItems) . " SPK (" . count($allMaterials) . " Item Material)";
        $this->pengajuanMaterials = $allMaterials;
        $this->pengajuanNotes = "Pengajuan Belanja Gabungan Finlog untuk " . count($this->selectedWaitingItems) . " SPK (" . implode(', ', $orders->pluck('spk_number')->toArray()) . ")";
        $this->showPengajuanModal = true;
    }

    public function openPengajuanModal($id)
    {
        $order = WorkOrder::with(['materials' => function($q) {
            $q->where('work_order_materials.status', '!=', 'RECEIVED');
        }])->find($id);

        if (!$order) return;

        $this->pengajuanOrderId = $id;
        $this->pengajuanSpkNumber = $order->spk_number;
        $this->pengajuanNotes = '';
        $this->pengajuanMaterials = $order->materials->map(fn($m) => [
            'material_id' => $m->id,
            'name'        => $m->name,
            'sub_category'=> $m->sub_category ?? '-',
            'unit'        => $m->unit ?? 'pcs',
            'quantity'    => $m->pivot->quantity,
            'price'       => $m->price ?? 0,
        ])->values()->toArray();

        $this->showPengajuanModal = true;
    }

    public function closePengajuanModal()
    {
        $this->showPengajuanModal = false;
        $this->pengajuanOrderId = null;
        $this->pengajuanMaterials = [];
        $this->pengajuanNotes = '';
        $this->isBulkPengajuan = false;
    }

    public function submitPengajuan(MaterialManagementService $service)
    {
        // 1. Bulk / Multi-SPK Shopping Request
        if ($this->isBulkPengajuan) {
            if (empty($this->pengajuanMaterials)) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak ada material untuk diajukan.']);
                return;
            }

            try {
                $items = collect($this->pengajuanMaterials)->map(fn($item) => [
                    'material'      => Material::find($item['material_id']),
                    'quantity'      => $item['quantity'],
                    'work_order_id' => $item['work_order_id'],
                ])->filter(fn($item) => $item['material'] !== null)->values()->toArray();

                $request = $service->createShoppingRequest(
                    $items,
                    null,
                    null,
                    $this->pengajuanNotes ?: "Pengajuan Belanja Gabungan Finlog (Multi-SPK)"
                );

                $spkCount = count($this->selectedWaitingItems);
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => "1 Nota Belanja Gabungan (#{$request->request_number}) berhasil dibuat untuk {$spkCount} SPK & dikirim ke Finlog!"
                ]);

                $this->selectedWaitingItems = [];
                $this->closePengajuanModal();
                return;
            } catch (\Exception $e) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal membuat pengajuan gabungan: ' . $e->getMessage()]);
                return;
            }
        }

        // 2. Single SPK Shopping Request
        if (!$this->pengajuanOrderId || empty($this->pengajuanMaterials)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak ada material untuk diajukan.']);
            return;
        }

        // Check if already has active request
        $order = WorkOrder::find($this->pengajuanOrderId);
        if (!$order) return;

        $alreadyExists = $order->materialRequests()
            ->whereIn('status', ['PENDING', 'APPROVED', 'PURCHASED'])
            ->exists();

        if ($alreadyExists) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'SPK ini sudah memiliki pengajuan belanja yang aktif.']);
            $this->closePengajuanModal();
            return;
        }

        try {
            $items = collect($this->pengajuanMaterials)->map(fn($item) => [
                'material'     => Material::find($item['material_id']),
                'quantity'     => $item['quantity'],
                'work_order_id'=> $this->pengajuanOrderId,
            ])->filter(fn($item) => $item['material'] !== null)->values()->toArray();

            $request = $service->createShoppingRequest(
                $items,
                $this->pengajuanOrderId,
                null,
                $this->pengajuanNotes ?: "Pengajuan belanja dari Tab Waiting Sortir — SPK #{$this->pengajuanSpkNumber}"
            );

            $this->dispatch('notify', ['type' => 'success', 'message' => "Pengajuan #{$request->request_number} berhasil dibuat & dikirim ke Finlog."]);
            $this->closePengajuanModal();
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal membuat pengajuan: ' . $e->getMessage()]);
        }
    }

    public function closeFollowUpModal()
    {
        $this->showFollowUpModal = false;
        $this->selectedOrderId = null;
        $this->detailKendala1 = '';
        $this->detailKendala2 = '';
        $this->opsiSolusi1 = '';
        $this->opsiSolusi2 = '';
        $this->catatanKendala = '';
    }

    public function submitFollowUp()
    {
        $this->validate([
            'detailKendala1' => 'required|string',
            'selectedOrderId' => 'required|exists:work_orders,id',
        ], [
            'detailKendala1.required' => 'Pilih Detail Kendala Pertama (wajib).',
        ]);

        try {
            $order = WorkOrder::findOrFail($this->selectedOrderId);
            $oldStatus = $order->status;

            $fullDesc = "Kategori: {$this->kategoriMasalah} | Kendala: {$this->detailKendala1}";
            if ($this->detailKendala2) {
                $fullDesc .= ", {$this->detailKendala2}";
            }
            if ($this->opsiSolusi1) {
                $fullDesc .= " | Solusi: {$this->opsiSolusi1}";
            }
            if ($this->opsiSolusi2) {
                $fullDesc .= ", {$this->opsiSolusi2}";
            }
            if ($this->estimasiWaktuTambahan) {
                $fullDesc .= " | Estimasi Tambahan: {$this->estimasiWaktuTambahan}";
            }
            if ($this->catatanKendala) {
                $fullDesc .= " | Catatan: {$this->catatanKendala}";
            }

            DB::transaction(function () use ($order, $oldStatus, $fullDesc) {
                $order->status = WorkOrderStatus::CX_FOLLOWUP;
                $order->current_location = 'Rak FU';
                $order->save();

                // Record issue to CxIssue model if available
                if (class_exists(\App\Models\CxIssue::class)) {
                    \App\Models\CxIssue::create([
                        'work_order_id' => $order->id,
                        'title' => 'Kendala Sortir: ' . $this->kategoriMasalah,
                        'issue_type' => 'FOLLOWUP_CUSTOMER',
                        'description' => $fullDesc,
                        'status' => 'OPEN',
                        'reported_by' => Auth::id(),
                    ]);
                }

                \App\Models\WorkOrderLog::create([
                    'work_order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'action' => 'SORTIR_FOLLOWUP_RAK_FU',
                    'description' => "Lapor Kendala / Rak FU: {$fullDesc}",
                    'step' => 'SORTIR'
                ]);
            });

            $this->dispatch('swal:toast', icon: 'success', title: "Laporan kendala SPK #{$order->spk_number} berhasil dikirim & dipindahkan ke Rak FU.");
            $this->closeFollowUpModal();
        } catch (\Exception $e) {
            $this->dispatch('swal:toast', icon: 'error', title: "Gagal memproses laporan: " . $e->getMessage());
        }
    }

    protected function applyFilters($query)
    {
        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterPriority) {
            if ($this->filterPriority === 'Prioritas') {
                $query->whereIn('priority', ['Prioritas', 'Urgent', 'Express', 'OTO']);
            } elseif ($this->filterPriority === 'Reguler') {
                $query->whereIn('priority', ['Reguler', 'Normal']);
            } else {
                $query->where('priority', $this->filterPriority);
            }
        }

        if ($this->filterBrand) {
            $query->where('shoe_brand', $this->filterBrand);
        }

        if ($this->filterType) {
            $query->where('shoe_type', $this->filterType);
        }

        // CX Resolved always absolute first
        $query->orderByRaw("CASE WHEN EXISTS (SELECT 1 FROM cx_issues WHERE cx_issues.work_order_id = work_orders.id AND cx_issues.status = 'RESOLVED') THEN 0 ELSE 1 END");

        // Fast Track always first
        $query->orderByRaw("CASE WHEN fast_track_status = 'yes' THEN 0 ELSE 1 END");

        // Sorting Logic
        switch ($this->sortBy) {
            case 'newest_spk':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest_spk':
                $query->orderBy('created_at', 'asc');
                break;
            case 'spk_asc':
                $query->orderBy('spk_number', 'asc');
                break;
            case 'priority_newest':
            default:
                $query->orderByRaw("CASE 
                    WHEN priority IN ('Prioritas', 'Urgent', 'Express', 'OTO') THEN 1 
                    ELSE 2 
                END ASC, created_at DESC");
                break;
        }

        return $query;
    }

    public function render(MaterialManagementService $materialService)
    {
        // 0. Proactive Auto-Allocation (ensure available stock is allocated)
        $materialService->autoAllocateStock();

        // Fetch Lists for Filters
        $baseQuery = WorkOrder::where('status', WorkOrderStatus::SORTIR->value)
            ->whereDoesntHave('logs', function($lq) {
                $lq->where('step', 'SORTIR')
                   ->where('action', 'CLASSIFICATION_COMPLETED');
            });
        
        $availableBrands = (clone $baseQuery)
            ->distinct()
            ->whereNotNull('shoe_brand')
            ->orderBy('shoe_brand')
            ->pluck('shoe_brand');

        $availableTypes = (clone $baseQuery)
            ->distinct()
            ->whereNotNull('shoe_type')
            ->orderBy('shoe_type')
            ->pluck('shoe_type');

        // 1. All Sortir Queue (Base)
        $allSortirQuery = WorkOrder::where('status', WorkOrderStatus::SORTIR->value)
            ->whereDoesntHave('logs', function($lq) {
                $lq->where('step', 'SORTIR')
                   ->where('action', 'CLASSIFICATION_COMPLETED');
            })
            ->with(['customer', 'services', 'materials', 'cxIssues', 'logs']);
        $allSortirOrders = $this->applyFilters(clone $allSortirQuery)->paginate(20, ['*'], 'allPage');

        // 2. Priority & Fast Track Queue
        $priorityQuery = WorkOrder::where('status', WorkOrderStatus::SORTIR->value)
            ->whereDoesntHave('logs', function($lq) {
                $lq->where('step', 'SORTIR')
                   ->where('action', 'CLASSIFICATION_COMPLETED');
            })
            ->where(function($q) {
                $q->whereIn('priority', ['Prioritas', 'Urgent', 'Express', 'OTO'])
                  ->orWhere('fast_track_status', 'yes')
                  ->orWhere('has_active_oto', true);
            })
            ->with(['customer', 'services', 'materials', 'cxIssues', 'logs']);
        $priorityOrders = $this->applyFilters(clone $priorityQuery)->paginate(20, ['*'], 'prioPage');

        // 3. Waiting Finlog Belanja Queue (Includes SPKs awaiting PO or currently in-flight with Finlog)
        $waitingQuery = WorkOrder::where('status', WorkOrderStatus::SORTIR->value)
            ->whereDoesntHave('logs', function($lq) {
                $lq->where('step', 'SORTIR')
                   ->where('action', 'CLASSIFICATION_COMPLETED');
            })
            ->where(function($q) {
                $q->where('perlu_belanja', true)
                  ->orWhereHas('materials', fn($m) => $m->where('work_order_materials.status', 'REQUESTED'))
                  ->orWhereHas('materialRequests', fn($mr) => $mr->whereNotIn('status', ['RECEIVED', 'CANCELLED', 'REJECTED']));
            })
            ->with(['customer', 'services', 'materials', 'cxIssues', 'logs', 'materialRequests']);
        $waitingOrders = $this->applyFilters(clone $waitingQuery)->paginate(20, ['*'], 'waitingPage');

        // 4. Ready / Standard Sortir Queue (exclude waiting orders)
        $readyQuery = WorkOrder::where('status', WorkOrderStatus::SORTIR->value)
            ->whereDoesntHave('logs', function($lq) {
                $lq->where('step', 'SORTIR')
                   ->where('action', 'CLASSIFICATION_COMPLETED');
            })
            ->where(function($q) {
                $q->where('perlu_belanja', '!=', true)
                  ->orWhereNull('perlu_belanja');
            })
            ->whereDoesntHave('materials', fn($m) => $m->where('work_order_materials.status', 'REQUESTED'))
            ->with(['customer', 'services', 'materials', 'cxIssues', 'logs']);
        $readyOrders = $this->applyFilters(clone $readyQuery)->paginate(20, ['*'], 'readyPage');

        // Metric calculations
        $totalCount = (clone $baseQuery)->count();
        $waitingCount = (clone $waitingQuery)->count();
        $priorityCount = (clone $priorityQuery)->count();
        $readyCount = $totalCount - $waitingCount;

        // ═══ KPI Metrics ═══
        // Daily Throughput: SPK completed sortir today
        $dailyThroughput = WorkOrder::where('status', WorkOrderStatus::PRODUCTION->value)
            ->whereDate('updated_at', today())
            ->count();

        // Average Lead Time in Sortir (hours)
        $avgLeadTimeHours = WorkOrder::where('status', WorkOrderStatus::SORTIR->value)
            ->whereNotNull('created_at')
            ->avg(DB::raw('TIMESTAMPDIFF(HOUR, created_at, NOW())'));

        // Active Bottleneck (CX Follow-Up + Waiting Belanja)
        $bottleneckFu = WorkOrder::where('status', WorkOrderStatus::CX_FOLLOWUP->value)
            ->where('current_location', 'Rak FU')
            ->count();
        $bottleneckCount = $bottleneckFu + $waitingCount;

        // ═══ Rack Capacity Data ═══
        $racks = StorageRack::active()->get()->map(function ($rack) {
            return [
                'rack_code' => $rack->rack_code,
                'location' => $rack->location,
                'category' => $rack->category?->value ?? $rack->category,
                'capacity' => $rack->capacity,
                'current_count' => $rack->current_count,
                'remaining' => $rack->getRemainingCapacity(),
                'utilization' => round($rack->getUtilizationPercentage(), 1),
                'is_over' => $rack->isOverCapacity(),
                'workshop_zone' => $rack->workshop_zone,
            ];
        });

        return view('livewire.sortir.index', [
            'allSortirOrders' => $allSortirOrders,
            'readyOrders' => $readyOrders,
            'waitingOrders' => $waitingOrders,
            'priorityOrders' => $priorityOrders,
            'readyCount' => $readyCount,
            'waitingCount' => $waitingCount,
            'priorityCount' => $priorityCount,
            'totalCount' => $totalCount,
            'availableBrands' => $availableBrands,
            'availableTypes' => $availableTypes,
            // KPI Metrics
            'dailyThroughput' => $dailyThroughput,
            'avgLeadTimeHours' => round($avgLeadTimeHours ?? 0, 1),
            'bottleneckCount' => $bottleneckCount,
            // Rack Capacity
            'racks' => $racks,
        ])->layout('layouts.workshop-pwa');
    }
}
