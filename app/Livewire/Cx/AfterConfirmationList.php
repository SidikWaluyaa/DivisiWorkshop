<?php

namespace App\Livewire\Cx;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\CxAfterConfirmation;
use App\Models\WorkOrder;
use App\Models\WorkOrderRevision;
use App\Models\OTO;
use App\Models\Service;
use App\Models\WorkOrderLog;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use App\Services\MaterialReservationService;
use App\Utils\ImageHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AfterConfirmationList extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $response = '';
    public $startDate = '';
    public $endDate = '';
    public $stageFilter = '';
    public $roundFilter = '';
    public $pickupFilter = '';
    public $perPage = 25;

    // Modal Edit Respon state
    public $editingId = null;
    public $editingResponse = '';
    public $editingNotes = '';
    public $selectedActionType = 'none'; // 'none', 'oto', 'revision'

    // Modal OTO state
    public $otoWoId = null;
    public $otoWorkOrder = null;

    // Modal Revisi state
    public $revisionWoId = null;
    public $revisionWorkOrder = null;
    public $revisionDescription = '';
    public $revisionPhotos = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'response' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'stageFilter' => ['except' => ''],
        'roundFilter' => ['except' => ''],
        'pickupFilter' => ['except' => ''],
        'perPage' => ['except' => 25],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingResponse()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function updatingStageFilter()
    {
        $this->resetPage();
    }

    public function updatingRoundFilter()
    {
        $this->resetPage();
    }

    public function updatingPickupFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'response', 'startDate', 'endDate', 'stageFilter', 'roundFilter', 'pickupFilter']);
        $this->resetPage();
    }

    public function setStageQuickFilter($stage = '')
    {
        $this->stageFilter = $stage;
        $this->roundFilter = '';
        $this->resetPage();
    }

    public function setRoundQuickFilter($round = '')
    {
        $this->roundFilter = $round;
        $this->stageFilter = '';
        $this->resetPage();
    }

    public function openActionModal($id)
    {
        $item = CxAfterConfirmation::with('workOrder')->findOrFail($id);
        $this->editingId = $id;
        $this->editingResponse = $item->response ?? '';
        $this->editingNotes = $item->notes ?? '';
        $this->selectedActionType = 'none';

        // Preload WorkOrder for OTO and Revisi
        $this->otoWoId = $item->work_order_id;
        $this->otoWorkOrder = $item->workOrder;

        $this->revisionWoId = $item->work_order_id;
        $this->revisionWorkOrder = $item->workOrder;
        $this->revisionDescription = '';
        $this->revisionPhotos = [];
        $this->resetValidation();

        $this->dispatch('open-unified-action-modal', [
            'confirmation' => [
                'id' => $item->id,
                'response' => $item->response,
                'notes' => $item->notes,
                'stage_badge' => $item->stage_badge,
                'round_badge' => $item->round_badge,
            ],
            'workOrder' => [
                'id' => $item->workOrder->id,
                'spk_number' => $item->workOrder->spk_number,
                'customer_name' => $item->workOrder->customer_name,
                'customer_phone' => $item->workOrder->customer_phone,
                'shoe_brand' => $item->workOrder->shoe_brand,
                'shoe_color' => $item->workOrder->shoe_color,
                'status' => $item->workOrder->status instanceof WorkOrderStatus ? $item->workOrder->status->value : $item->workOrder->status,
                'taken_date' => $item->workOrder->taken_date ? $item->workOrder->taken_date->format('d M Y, H:i') : null,
            ]
        ]);
    }

    public function edit($id)
    {
        return $this->openActionModal($id);
    }

    public function saveAction($otoPayload = [])
    {
        $this->validate([
            'editingResponse' => 'nullable|in:Puas,Komplain,Kurang Puas,No Respon 1x24 Jam,Hold',
            'editingNotes' => 'nullable|string',
            'selectedActionType' => 'required|in:none,oto,revision',
        ]);

        if (!$this->editingId) {
            $this->dispatch('notify', ['message' => 'Data konfirmasi tidak ditemukan.']);
            return;
        }

        $item = CxAfterConfirmation::with('workOrder')->findOrFail($this->editingId);
        $workOrder = $item->workOrder;

        // Validasi OTO jika dipilih
        if ($this->selectedActionType === 'oto') {
            if ($workOrder->status !== WorkOrderStatus::SELESAI) {
                $this->dispatch('notify', ['message' => 'Order harus dalam status SELESAI untuk membuat OTO.']);
                return;
            }
            $services = $otoPayload['services'] ?? [];
            if (empty($services)) {
                $this->dispatch('notify', ['message' => 'Pilih minimal satu layanan OTO pada accordion OTO.']);
                return;
            }
            $otoDesc = trim($otoPayload['description'] ?? '');
            if (!$otoDesc) {
                $this->dispatch('notify', ['message' => 'Alasan penawaran OTO wajib diisi pada accordion OTO.']);
                return;
            }
        } elseif ($this->selectedActionType === 'revision') {
            $this->validate([
                'revisionDescription' => 'required|string|min:5',
                'revisionPhotos.*' => 'nullable|image|max:5120',
            ], [
                'revisionDescription.required' => 'Deskripsi masalah / alasan revisi wajib diisi pada accordion Revisi.',
                'revisionDescription.min' => 'Deskripsi minimal 5 karakter.',
                'revisionPhotos.*.image' => 'File harus berupa gambar.',
                'revisionPhotos.*.max' => 'Ukuran gambar maksimal 5MB.',
            ]);
        }

        try {
            DB::transaction(function () use ($item, $workOrder, $otoPayload) {
                // 1. Update Respon CxAfterConfirmation
                $data = [
                    'response' => $this->editingResponse,
                    'notes' => $this->editingNotes,
                ];
                if (!empty($this->editingResponse) && is_null($item->pic_id)) {
                    $data['pic_id'] = Auth::id();
                    $data['contacted_at'] = now();
                }
                $item->update($data);

                // 2. Eksekusi OTO jika dipilih
                if ($this->selectedActionType === 'oto') {
                    $services = $otoPayload['services'] ?? [];
                    $otoDesc = trim($otoPayload['description'] ?? '');
                    $serviceNames = [];
                    $totalNormal = 0;
                    $totalOTO = 0;
                    $totalOtoHk = 0;

                    foreach ($services as $serviceData) {
                        $service = Service::findOrFail($serviceData['id']);
                        $serviceNames[] = $service->name;
                        $totalNormal += (float) ($serviceData['normal_price'] ?? $service->price);
                        $totalOTO += (float) ($serviceData['oto_price'] ?? $service->price);
                        $totalOtoHk += (int) ($serviceData['hk_days'] ?? 0);
                    }

                    $totalDiscount = $totalNormal - $totalOTO;
                    $discountPercent = $totalNormal > 0 ? ($totalDiscount / $totalNormal) * 100 : 0;
                    $formatPrice = fn($val) => 'Rp. ' . number_format((float)$val, 0, ',', '.');
                    $validDays = max((int) ($otoPayload['valid_days'] ?? 5), 1);

                    $oto = OTO::create([
                        'work_order_id' => $workOrder->id,
                        'spk_number' => $workOrder->spk_number,
                        'customer_name' => $workOrder->customer_name,
                        'customer_phone' => $workOrder->customer_phone,
                        'title' => 'Penawaran Jasa Tambahan untuk ' . $workOrder->customer_name,
                        'description' => $otoDesc,
                        'oto_type' => 'UPSELL',
                        'proposed_services' => implode(', ', $serviceNames),
                        'total_normal_price' => $formatPrice($totalNormal),
                        'total_oto_price' => $formatPrice($totalOTO),
                        'total_discount' => $formatPrice($totalDiscount),
                        'discount_percent' => round($discountPercent, 2),
                        'estimated_days' => $totalOtoHk,
                        'valid_until' => now()->addDays($validDays),
                        'status' => 'PENDING_CX',
                        'dp_required' => $formatPrice($totalOTO * 0.5),
                        'send_automation' => !empty($otoPayload['send_automation']),
                        'created_by' => Auth::id(),
                    ]);

                    app(MaterialReservationService::class)->softReserveForOTO($oto);

                    $workOrder->update([
                        'has_active_oto' => false,
                    ]);

                    WorkOrderLog::create([
                        'work_order_id' => $workOrder->id,
                        'user_id' => Auth::id(),
                        'step' => $workOrder->status instanceof WorkOrderStatus ? $workOrder->status->value : $workOrder->status,
                        'action' => 'OTO_CREATED',
                        'description' => "Penawaran OTO dibuat berbarengan dari Konfirmasi After: " . implode(', ', $serviceNames) . " (Harga OTO: " . $formatPrice($totalOTO) . ", Diskon: " . round($discountPercent) . "%)"
                    ]);
                }

                // 3. Eksekusi Revisi jika dipilih
                if ($this->selectedActionType === 'revision') {
                    app(WorkflowService::class)->updateStatus(
                        $workOrder,
                        WorkOrderStatus::REVISI,
                        'Mengajukan revisi teknik berbarengan dari Konfirmasi After: ' . $this->revisionDescription,
                        Auth::id()
                    );

                    $photoPaths = [];
                    if (!empty($this->revisionPhotos)) {
                        $photoArray = is_array($this->revisionPhotos) ? $this->revisionPhotos : [$this->revisionPhotos];
                        foreach ($photoArray as $index => $photo) {
                            if ($photo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                $filename = 'REV_REQ_' . $workOrder->spk_number . '_' . time() . '_' . $index;
                                $path = ImageHelper::convertToJpg($photo, 'photos/qc_reject', $filename);
                                $photoPaths[] = $path;
                            }
                        }
                    }

                    WorkOrderRevision::create([
                        'work_order_id' => $workOrder->id,
                        'description' => $this->revisionDescription,
                        'photo_path' => $photoPaths[0] ?? null,
                        'photo_paths' => $photoPaths,
                        'origin_status' => 'SELESAI',
                        'status' => 'OPEN',
                        'created_by' => Auth::id(),
                    ]);
                }
            });

            $successMsg = 'Data konfirmasi berhasil diperbarui.';
            if ($this->selectedActionType === 'oto') {
                $successMsg = 'Respon konfirmasi & penawaran OTO berhasil disimpan sekaligus!';
            } elseif ($this->selectedActionType === 'revision') {
                $successMsg = 'Respon konfirmasi & pengajuan revisi ke workshop berhasil disimpan sekaligus!';
            }

            $this->editingId = null;
            $this->selectedActionType = 'none';
            $this->revisionDescription = '';
            $this->revisionPhotos = [];
            $this->otoWoId = null;
            $this->otoWorkOrder = null;
            $this->revisionWoId = null;
            $this->revisionWorkOrder = null;

            $this->dispatch('close-unified-action-modal');
            $this->dispatch('close-edit-modal');
            $this->dispatch('notify', ['message' => $successMsg]);

        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Gagal memproses data: ' . $e->getMessage()]);
        }
    }

    public function save()
    {
        return $this->saveAction();
    }

    public function openOtoModal($workOrderId)
    {
        $this->otoWoId = $workOrderId;
        $this->otoWorkOrder = WorkOrder::findOrFail($workOrderId);
        $this->dispatch('close-edit-modal');
        $this->dispatch('open-oto-modal', [
            'workOrder' => [
                'id' => $this->otoWorkOrder->id,
                'spk_number' => $this->otoWorkOrder->spk_number,
                'customer_name' => $this->otoWorkOrder->customer_name,
                'customer_phone' => $this->otoWorkOrder->customer_phone,
                'shoe_brand' => $this->otoWorkOrder->shoe_brand,
            ]
        ]);
    }

    public function submitOto($data)
    {
        if (!$this->otoWoId) {
            $this->dispatch('notify', ['message' => 'Data Work Order tidak ditemukan.']);
            return;
        }

        $workOrder = WorkOrder::findOrFail($this->otoWoId);

        if ($workOrder->status !== WorkOrderStatus::SELESAI) {
            $this->dispatch('notify', ['message' => 'Order harus dalam status SELESAI untuk membuat OTO.']);
            return;
        }

        $services = $data['services'] ?? [];
        if (empty($services)) {
            $this->dispatch('notify', ['message' => 'Pilih minimal satu layanan OTO.']);
            return;
        }

        $description = trim($data['description'] ?? '');
        if (!$description) {
            $this->dispatch('notify', ['message' => 'Alasan penawaran OTO wajib diisi.']);
            return;
        }

        try {
            DB::transaction(function() use ($data, $workOrder, $description, $services) {
                $serviceNames = [];
                $totalNormal = 0;
                $totalOTO = 0;
                $totalOtoHk = 0;

                foreach ($services as $serviceData) {
                    $service = Service::findOrFail($serviceData['id']);
                    $serviceNames[] = $service->name;
                    $totalNormal += (float) ($serviceData['normal_price'] ?? $service->price);
                    $totalOTO += (float) ($serviceData['oto_price'] ?? $service->price);
                    $totalOtoHk += (int) ($serviceData['hk_days'] ?? 0);
                }

                $totalDiscount = $totalNormal - $totalOTO;
                $discountPercent = $totalNormal > 0 ? ($totalDiscount / $totalNormal) * 100 : 0;
                $formatPrice = fn($val) => 'Rp. ' . number_format((float)$val, 0, ',', '.');
                $validDays = max((int) ($data['valid_days'] ?? 5), 1);

                $oto = OTO::create([
                    'work_order_id' => $workOrder->id,
                    'spk_number' => $workOrder->spk_number,
                    'customer_name' => $workOrder->customer_name,
                    'customer_phone' => $workOrder->customer_phone,
                    'title' => 'Penawaran Jasa Tambahan untuk ' . $workOrder->customer_name,
                    'description' => $description,
                    'oto_type' => 'UPSELL',
                    'proposed_services' => implode(', ', $serviceNames),
                    'total_normal_price' => $formatPrice($totalNormal),
                    'total_oto_price' => $formatPrice($totalOTO),
                    'total_discount' => $formatPrice($totalDiscount),
                    'discount_percent' => round($discountPercent, 2),
                    'estimated_days' => $totalOtoHk,
                    'valid_until' => now()->addDays($validDays),
                    'status' => 'PENDING_CX',
                    'dp_required' => $formatPrice($totalOTO * 0.5),
                    'send_automation' => !empty($data['send_automation']),
                    'created_by' => Auth::id(),
                ]);

                // Soft reserve materials
                app(MaterialReservationService::class)->softReserveForOTO($oto);

                // Keep has_active_oto as false so the SPK remains visible in Gudang Selesai
                $workOrder->update([
                    'has_active_oto' => false,
                ]);

                // Work order audit log
                WorkOrderLog::create([
                    'work_order_id' => $workOrder->id,
                    'user_id' => Auth::id(),
                    'step' => $workOrder->status instanceof WorkOrderStatus ? $workOrder->status->value : $workOrder->status,
                    'action' => 'OTO_CREATED',
                    'description' => "Penawaran OTO dibuat dari Konfirmasi After: " . implode(', ', $serviceNames) . " (Harga OTO: " . $formatPrice($totalOTO) . ", Diskon: " . round($discountPercent) . "%)"
                ]);
            });

            $this->otoWoId = null;
            $this->otoWorkOrder = null;
            $this->dispatch('close-oto-modal');
            $this->dispatch('notify', ['message' => 'Penawaran OTO berhasil dibuat dan masuk ke Kolam Antrean CX.']);

        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Gagal membuat OTO: ' . $e->getMessage()]);
        }
    }

    public function openRevisionModal($workOrderId)
    {
        $this->revisionWoId = $workOrderId;
        $this->revisionWorkOrder = WorkOrder::findOrFail($workOrderId);
        $this->revisionDescription = '';
        $this->revisionPhotos = [];
        $this->resetValidation();
        $this->dispatch('close-edit-modal');
        $this->dispatch('open-revision-modal');
    }

    public function submitRevision()
    {
        $this->validate([
            'revisionDescription' => 'required|string|min:5',
            'revisionPhotos.*' => 'nullable|image|max:5120',
        ], [
            'revisionDescription.required' => 'Deskripsi masalah / alasan revisi wajib diisi.',
            'revisionDescription.min' => 'Deskripsi minimal 5 karakter.',
            'revisionPhotos.*.image' => 'File harus berupa gambar.',
            'revisionPhotos.*.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        if (!$this->revisionWoId) {
            $this->dispatch('notify', ['message' => 'Data Work Order tidak ditemukan.']);
            return;
        }

        $workOrder = WorkOrder::findOrFail($this->revisionWoId);

        try {
            DB::transaction(function () use ($workOrder) {
                // 1. Move WorkOrder status to REVISI
                app(WorkflowService::class)->updateStatus(
                    $workOrder,
                    WorkOrderStatus::REVISI,
                    'Mengajukan revisi teknik dari Konfirmasi After: ' . $this->revisionDescription,
                    Auth::id()
                );

                // 2. Handle Multiple Photo Uploads with ImageHelper compression
                $photoPaths = [];
                if (!empty($this->revisionPhotos)) {
                    $photoArray = is_array($this->revisionPhotos) ? $this->revisionPhotos : [$this->revisionPhotos];
                    foreach ($photoArray as $index => $photo) {
                        if ($photo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            $filename = 'REV_REQ_' . $workOrder->spk_number . '_' . time() . '_' . $index;
                            $path = ImageHelper::convertToJpg($photo, 'photos/qc_reject', $filename);
                            $photoPaths[] = $path;
                        }
                    }
                }

                // 3. Create Revision Record with origin_status = 'SELESAI'
                WorkOrderRevision::create([
                    'work_order_id' => $workOrder->id,
                    'description' => $this->revisionDescription,
                    'photo_path' => $photoPaths[0] ?? null,
                    'photo_paths' => $photoPaths,
                    'origin_status' => 'SELESAI',
                    'status' => 'OPEN',
                    'created_by' => Auth::id(),
                ]);
            });

            $this->revisionDescription = '';
            $this->revisionPhotos = [];
            $this->revisionWoId = null;
            $this->revisionWorkOrder = null;

            $this->dispatch('close-revision-modal');
            $this->dispatch('notify', ['message' => 'Revisi teknik berhasil diajukan dan masuk ke antrean workshop.']);

        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Gagal mengajukan revisi: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $query = CxAfterConfirmation::with(['workOrder', 'pic'])
            ->orderBy('entered_at', 'desc');

        // Filter by Date
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('entered_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }

        // Filter by Response
        if ($this->response) {
            $query->where('response', $this->response);
        }

        // Filter by Stage Type (Garansi, Revisi, OTO, R&D, Reguler)
        // Menampilkan seluruh siklus pengerjaan (After 1 Reguler, After 2 OTO/Revisi/Garansi, dst.) milik SPK terkait
        if ($this->stageFilter) {
            $stage = strtoupper(trim($this->stageFilter));
            if ($stage === 'REGULER') {
                $query->where('stage_type', 'REGULER');
            } else {
                $query->whereHas('workOrder.afterConfirmations', function ($q) use ($stage) {
                    $q->where('stage_type', $stage);
                });
            }
        }

        // Filter by Round (After 1 / After 2 / After 3 / After 2+)
        if ($this->roundFilter) {
            if ($this->roundFilter === '2+') {
                $query->where('round', '>=', 2);
            } else {
                $query->where('round', (int)$this->roundFilter);
            }
        }

        // Filter by Pickup Status (Sudah Diambil / Belum Diambil)
        if ($this->pickupFilter === 'taken') {
            $query->whereHas('workOrder', function ($q) {
                $q->whereNotNull('taken_date');
            });
        } elseif ($this->pickupFilter === 'not_taken') {
            $query->whereHas('workOrder', function ($q) {
                $q->whereNull('taken_date');
            });
        }

        // Search by SPK, Customer Name, or Phone
        if ($this->search) {
            $search = $this->search;
            $query->whereHas('workOrder', function ($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Dynamic stats filtered by active date range
        $statsBase = CxAfterConfirmation::query();
        if ($this->startDate && $this->endDate) {
            $statsBase->whereBetween('entered_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }

        $stats = [
            'total'   => (clone $statsBase)->count(),
            'after1'  => (clone $statsBase)->where('round', 1)->count(),
            'after2'  => (clone $statsBase)->where('round', '>=', 2)->count(),
            'reguler' => (clone $statsBase)->where('stage_type', 'REGULER')->count(),
            'garansi' => (clone $statsBase)->where('stage_type', 'GARANSI')->count(),
            'revisi'  => (clone $statsBase)->where('stage_type', 'REVISI')->count(),
            'oto'     => (clone $statsBase)->where('stage_type', 'OTO')->count(),
            'rnd'     => (clone $statsBase)->where('stage_type', 'RND')->count(),
        ];

        return view('livewire.cx.after-confirmation-list', [
            'items' => $query->paginate((int)$this->perPage),
            'stats' => $stats,
            'availableOtoServices' => Service::where('category', 'OTO')->get(),
        ]);
    }
}
