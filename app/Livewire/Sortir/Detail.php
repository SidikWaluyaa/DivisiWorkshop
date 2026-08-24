<?php

namespace App\Livewire\Sortir;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\WorkOrder;
use App\Models\Material;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use App\Services\WorkflowService;
use App\Services\MaterialManagementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Detail extends Component
{
    use WithFileUploads;

    public $orderId;
    public $order;
    public $selectedMaterials = [];
    public $pic_sortir_sol_id;
    public $pic_sortir_upper_id;

    // Sortir Classification (GAP PRD-SRS FR-3.1)
    public $perlu_bongkar = false;
    public $perlu_belanja = false;
    public $bypass_reason = '';

    // Upsell state
    public $showUpsellModal = false;
    public $upsellServiceId;
    public $upsellCustomName;
    public $upsellCustomPrice;
    public $upsellCategory;
    public $upsellNotes;
    public $upsellPhoto;

    // UI State
    public $activeTab = 'upper';
    public $searchMaterial = '';

    // Bongkar (Disassembly) technician assignments
    public $bongkar_sol_tech_id;
    public $bongkar_upper_tech_id;

    protected $listeners = ['refreshDetail' => '$refresh'];

    public function mount($id)
    {
        $this->orderId = $id;
        $this->loadOrder();
        $this->initializeState();
        $this->runSelfHealing();
    }

    public function loadOrder()
    {
        $this->order = WorkOrder::with(['customer', 'materials', 'services', 'logs', 'cxIssues'])->findOrFail($this->orderId);
    }

    public function initializeState()
    {
        $this->selectedMaterials = [];
        foreach ($this->order->materials as $mat) {
            $this->selectedMaterials[$mat->id] = [
                'material_id' => $mat->id,
                'name' => $mat->name,
                'quantity' => $mat->pivot->quantity,
                'status' => $mat->pivot->status,
                'price' => $mat->price,
                'type' => $mat->type
            ];
        }

        $this->pic_sortir_sol_id = $this->order->pic_sortir_sol_id;
        $this->pic_sortir_upper_id = $this->order->pic_sortir_upper_id;

        $this->perlu_bongkar = (bool) $this->order->perlu_bongkar;
        $this->perlu_belanja = (bool) $this->order->perlu_belanja;

        // Bongkar tech assignments (reusing prep_sol_by / prep_upper_by columns for Sortir Bongkar)
        $this->bongkar_sol_tech_id = $this->order->prep_sol_by;
        $this->bongkar_upper_tech_id = $this->order->prep_upper_by;

        // Suggested tab logic
        $hasSolService = $this->order->services->contains(function($service) {
            $cat = strtolower($service->category);
            return str_contains($cat, 'sol') || str_contains($cat, 'midsole') || str_contains($cat, 'paket');
        });
        $this->activeTab = $hasSolService ? 'sol' : 'upper';
    }

    public function updatedPerluBongkar($value)
    {
        $this->perlu_bongkar = (bool) $value;
        $this->order->update(['perlu_bongkar' => $this->perlu_bongkar]);
        $this->dispatch('notify', type: 'success', message: 'Pilihan Perlu Bongkar berhasil disimpan.');
    }

    public function updatedPerluBelanja($value)
    {
        $this->perlu_belanja = (bool) $value;
        $this->order->update(['perlu_belanja' => $this->perlu_belanja]);
        $this->dispatch('notify', type: 'success', message: 'Pilihan Perlu Belanja berhasil disimpan.');
    }

    public function updatedPicSortirSolId($value)
    {
        $this->order->update(['pic_sortir_sol_id' => $value ?: null]);
    }

    public function updatedPicSortirUpperId($value)
    {
        $this->order->update(['pic_sortir_upper_id' => $value ?: null]);
    }

    public function runSelfHealing()
    {
        $materialService = app(MaterialManagementService::class);

        // Auto-import requested materials from CS SPK if work_order_materials is empty
        if ($this->order->materials->isEmpty()) {
            $csSpk = \App\Models\CsSpk::where('spk_number', $this->order->spk_number)->with('items')->first();
            if ($csSpk) {
                $csMaterials = [];
                if (!empty($csSpk->requested_materials) && is_array($csSpk->requested_materials)) {
                    $csMaterials = array_merge($csMaterials, $csSpk->requested_materials);
                }
                foreach ($csSpk->items as $item) {
                    if (!empty($item->requested_materials) && is_array($item->requested_materials)) {
                        $csMaterials = array_merge($csMaterials, $item->requested_materials);
                    }
                }

                if (!empty($csMaterials)) {
                    $hasMissingStock = false;

                    foreach ($csMaterials as $matData) {
                        $matId = $matData['material_id'] ?? null;
                        $matQty = (int)($matData['quantity'] ?? 1);
                        if (!$matId) continue;

                        $material = Material::find($matId);
                        if (!$material) continue;

                        $status = 'ALLOCATED';
                        if ($material->stock < $matQty) {
                            $status = 'REQUESTED';
                            $hasMissingStock = true;
                        } else {
                            $materialService->logTransaction(
                                $material, 
                                'OUT', 
                                $matQty, 
                                'WorkOrder', 
                                $this->order->id, 
                                "Auto-allocated from CS Material Request for SPK #{$this->order->spk_number}"
                            );
                            $material->decrement('stock', $matQty);
                        }

                        $this->order->materials()->syncWithoutDetaching([
                            $matId => [
                                'quantity' => $matQty,
                                'status' => $status
                            ]
                        ]);
                    }

                    if ($hasMissingStock) {
                        $this->order->update(['perlu_belanja' => true]);
                        $this->perlu_belanja = true;
                    }
                }
            }
        }

        $materialService->autoAllocateStock();
        
        $this->loadOrder();
        $this->initializeState();
    }

    public function addMaterial($id)
    {
        $material = Material::find($id);
        if (!$material) return;

        if (isset($this->selectedMaterials[$id])) {
            $this->selectedMaterials[$id]['quantity']++;
        } else {
            $this->selectedMaterials[$id] = [
                'material_id' => $id,
                'name' => $material->name,
                'quantity' => 1,
                'status' => 'PENDING_SAVE',
                'price' => $material->price,
                'type' => $material->type
            ];
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $material->name . ' ditambahkan ke daftar.'
        ]);
    }

    public function updateQuantity($id, $qty)
    {
        if ($qty < 1) return;
        if (isset($this->selectedMaterials[$id])) {
            $this->selectedMaterials[$id]['quantity'] = (int)$qty;
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Jumlah material diperbarui.'
            ]);
        }
    }

    public function removeMaterial($id)
    {
        if (isset($this->selectedMaterials[$id])) {
            $matName = $this->selectedMaterials[$id]['name'];
            unset($this->selectedMaterials[$id]);
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => $matName . ' dihapus dari daftar.'
            ]);
        }
    }

    public function saveMaterials()
    {
        $materialService = app(MaterialManagementService::class);
        
        try {
            DB::transaction(function() use ($materialService) {
                // 1. Get current state from DB
                $currentMaterials = $this->order->materials->keyBy('id');
                $newMaterials = collect($this->selectedMaterials);
                
                // 2. Handle Removals
                foreach ($currentMaterials as $matId => $mat) {
                    if (!$newMaterials->has($matId)) {
                        if ($mat->pivot->status == 'ALLOCATED') {
                            $mat->increment('stock', $mat->pivot->quantity);
                            $materialService->logTransaction($mat, 'IN', $mat->pivot->quantity, 'WorkOrder', $this->order->id, "Removed in Sortir (Livewire) for SPK #{$this->order->spk_number}");
                        }
                        $this->order->materials()->detach($matId);
                    }
                }

                // 3. Handle Additions & Updates
                foreach ($newMaterials as $matId => $data) {
                    $newQty = (int) $data['quantity'];
                    
                    if ($currentMaterials->has($matId)) {
                        // Update Existing
                        $currentMat = $currentMaterials->get($matId);
                        $oldQty = (int) $currentMat->pivot->quantity;
                        $diff = $newQty - $oldQty;
                        
                        if ($diff != 0) {
                            $currentMatModel = Material::find($matId);
                            if ($currentMat->pivot->status == 'ALLOCATED') {
                                if ($diff > 0) {
                                    $materialService->logTransaction($currentMatModel, 'OUT', $diff, 'WorkOrder', $this->order->id, "Increased quantity in Sortir (Livewire) for SPK #{$this->order->spk_number}");
                                    $currentMatModel->decrement('stock', $diff);
                                } else {
                                    $restoreQty = abs($diff);
                                    $materialService->logTransaction($currentMatModel, 'IN', $restoreQty, 'WorkOrder', $this->order->id, "Reduced quantity in Sortir (Livewire) for SPK #{$this->order->spk_number}");
                                    $currentMatModel->increment('stock', $restoreQty);
                                }
                            }
                            $this->order->materials()->updateExistingPivot($matId, ['quantity' => $newQty]);
                        }
                    } else {
                        // New Addition
                        $material = Material::find($matId);
                        $status = 'ALLOCATED'; 
                        
                        if ($material->stock < $newQty) {
                             $status = 'REQUESTED';
                        } else {
                             $materialService->logTransaction($material, 'OUT', $newQty, 'WorkOrder', $this->order->id, "Added in Sortir (Livewire) for SPK #{$this->order->spk_number}");
                             $material->decrement('stock', $newQty);
                        }
                        
                        $this->order->materials()->attach($matId, [
                            'quantity' => $newQty,
                            'status' => $status
                        ]);
                    }
                }
            });

            $this->loadOrder();
            $this->initializeState();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Daftar material berhasil diperbarui.']);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal simpan: ' . $e->getMessage()]);
        }
    }

    public function processUpsell()
    {
        $this->validate([
            'upsellServiceId' => 'required',
            'upsellPhoto' => 'nullable|image|max:5120',
        ]);

        if ($this->upsellServiceId === 'custom') {
            $this->validate([
                'upsellCustomName' => 'required|string|max:255',
                'upsellCustomPrice' => 'required|numeric|min:0',
                'upsellCategory' => 'required|string|max:100',
            ]);
        }

        try {
            DB::transaction(function() {
                $serviceId = null;
                $cost = 0;
                $category = 'Custom';
                $name = 'Custom Service';
                $customName = null;

                if ($this->upsellServiceId === 'custom') {
                    $serviceId = null;
                    $name = $this->upsellCustomName;
                    $customName = $this->upsellCustomName;
                    $cost = $this->upsellCustomPrice;
                    $category = $this->upsellCategory;
                } else {
                    $service = \App\Models\Service::findOrFail($this->upsellServiceId);
                    $serviceId = $service->id;
                    $name = $service->name;
                    $category = $service->category;
                    $cost = $this->upsellCustomPrice ?? $service->price;
                    $customName = $this->upsellCustomName;
                }

                $this->order->workOrderServices()->create([
                    'service_id' => $serviceId,
                    'cost' => $cost,
                    'category_name' => $category,
                    'custom_service_name' => $customName,
                    'notes' => $this->upsellNotes,
                    'status' => 'pending'
                ]);

                $this->order->status = WorkOrderStatus::PREPARATION;
                
                $cat = strtolower($category);
                if (str_contains($cat, 'sol')) { $this->order->prod_sol_started_at = null; $this->order->prod_sol_completed_at = null; }
                if (str_contains($cat, 'upper')) { $this->order->prod_upper_started_at = null; $this->order->prod_upper_completed_at = null; }
                if (str_contains($cat, 'cleaning')) { $this->order->prod_cleaning_started_at = null; $this->order->prod_cleaning_completed_at = null; }

                $this->order->save();

                $this->order->logs()->create([
                     'step' => WorkOrderStatus::PREPARATION->value,
                     'action' => 'UPSELL',
                     'user_id' => Auth::id(),
                     'description' => "Added Service in Sortir (Livewire): {$name} ({$category}). Order reset to PREPARATION."
                ]);

                if ($this->upsellPhoto) {
                    $filename = 'UPSELL_LW_' . $this->order->spk_number . '_' . time() . '.' . $this->upsellPhoto->getClientOriginalExtension();
                    $path = $this->upsellPhoto->storeAs('photos/upsell', $filename, 'public');

                    \App\Models\WorkOrderPhoto::create([
                        'work_order_id' => $this->order->id,
                        'step' => 'UPSELL_SORTIR_BEFORE', 
                        'file_path' => $path,
                        'is_public' => true,
                    ]);
                }
            });

            return redirect()->route('sortir.index')->with('success', 'Layanan berhasil ditambahkan. Order kembali ke status Preparation.');

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal upsell: ' . $e->getMessage()]);
        }
    }

    /**
     * Start Bongkar (Disassembly) for Sol or Upper
     */
    public function startBongkar($type)
    {
        if (!in_array($type, ['sol', 'upper'])) return;

        $startedCol = "prep_{$type}_started_at";
        $techCol = "prep_{$type}_by";
        $techProp = "bongkar_{$type}_tech_id";

        if ($this->order->$startedCol) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Bongkar ' . ucfirst($type) . ' sudah dimulai.']);
            return;
        }

        $techId = $this->$techProp ?? Auth::id();

        $this->order->update([
            $startedCol => now(),
            $techCol => $techId,
        ]);

        $this->order->logs()->create([
            'user_id' => Auth::id(),
            'step' => 'SORTIR_BONGKAR',
            'action' => "bongkar_{$type}_start",
            'description' => 'Memulai proses Bongkar ' . ucfirst($type) . ' (Sortir Stage)',
        ]);

        $this->loadOrder();
        $this->initializeState();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Bongkar ' . ucfirst($type) . ' dimulai.']);
    }

    /**
     * Finish Bongkar (Disassembly) for Sol or Upper
     */
    public function finishBongkar($type)
    {
        if (!in_array($type, ['sol', 'upper'])) return;

        $startedCol = "prep_{$type}_started_at";
        $completedCol = "prep_{$type}_completed_at";
        $techCol = "prep_{$type}_by";
        $techProp = "bongkar_{$type}_tech_id";

        if (!$this->order->$startedCol) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Bongkar ' . ucfirst($type) . ' belum dimulai.']);
            return;
        }

        if ($this->order->$completedCol) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Bongkar ' . ucfirst($type) . ' sudah selesai.']);
            return;
        }

        $techId = $this->$techProp ?? Auth::id();

        $this->order->update([
            $completedCol => now(),
            $techCol => $techId,
        ]);

        $this->order->logs()->create([
            'user_id' => Auth::id(),
            'step' => 'SORTIR_BONGKAR',
            'action' => "bongkar_{$type}_finish",
            'description' => 'Menyelesaikan proses Bongkar ' . ucfirst($type) . ' (Sortir Stage)',
        ]);

        $this->loadOrder();
        $this->initializeState();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Bongkar ' . ucfirst($type) . ' selesai.']);
    }

    /**
     * Check if this SPK needs Bongkar Sol (has sol-related services)
     */
    public function getNeedsBongkarSolProperty()
    {
        return $this->order->services->contains(function ($service) {
            $cat = strtolower($service->category ?? '');
            return str_contains($cat, 'sol') || str_contains($cat, 'midsole') || str_contains($cat, 'paket');
        });
    }

    /**
     * Check if this SPK needs Bongkar Upper (has upper/repaint-related services)
     */
    public function getNeedsBongkarUpperProperty()
    {
        return $this->order->services->contains(function ($service) {
            $cat = strtolower($service->category ?? '');
            return str_contains($cat, 'upper') || str_contains($cat, 'repaint') || str_contains($cat, 'jahit');
        });
    }

    public function saveDraft()
    {
        try {
            $this->order->update([
                'pic_sortir_sol_id' => $this->pic_sortir_sol_id,
                'pic_sortir_upper_id' => $this->pic_sortir_upper_id,
                'perlu_bongkar' => $this->perlu_bongkar,
                'perlu_belanja' => $this->perlu_belanja,
            ]);

            $this->saveMaterials();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Draft klasifikasi & material SPK #' . $this->order->spk_number . ' berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Gagal menyimpan draft: ' . $e->getMessage()
            ]);
        }
    }

    public function completeSortir()
    {
        $this->validate([
            'pic_sortir_sol_id' => 'nullable|exists:users,id',
            'pic_sortir_upper_id' => 'nullable|exists:users,id',
            'perlu_bongkar' => 'required|boolean',
            'perlu_belanja' => 'required|boolean',
        ]);

        try {
            // 1. Auto-save classification & materials first
            $this->order->update([
                'pic_sortir_sol_id' => $this->pic_sortir_sol_id,
                'pic_sortir_upper_id' => $this->pic_sortir_upper_id,
                'perlu_bongkar' => $this->perlu_bongkar,
                'perlu_belanja' => $this->perlu_belanja,
            ]);

            $this->saveMaterials();

            // 2. Hard-block validation for Bongkar
            if ($this->perlu_bongkar) {
                $blocked = [];
                if ($this->needsBongkarSol && !$this->order->prep_sol_completed_at) {
                    $blocked[] = 'Bongkar Sol';
                }
                if ($this->needsBongkarUpper && !$this->order->prep_upper_completed_at) {
                    $blocked[] = 'Bongkar Upper';
                }
                if (!empty($blocked)) {
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => 'Tidak dapat menyelesaikan Sortir. Proses berikut belum selesai: ' . implode(', ', $blocked)
                    ]);
                    return;
                }
            }

            $workflow = app(WorkflowService::class);
            $materialService = app(MaterialManagementService::class);

            if ($this->order->is_revising && $this->order->previous_status instanceof WorkOrderStatus) {
                $targetStatus = $this->order->previous_status;
                $workflow->updateStatus($this->order, $targetStatus, "Revision completed in Sortir (Livewire). Returning to " . $targetStatus->value);
                $this->order->update(['is_revising' => false, 'previous_status' => null]);
                return redirect()->route('sortir.index')->with('success', 'Revisi selesai. Sepatu kembali ke ' . $targetStatus->value);
            }

            // 3. Routing: cek apakah material sudah ALLOCATED/RECEIVED (override perlu_belanja)
            $hasAllocatedMaterial = $this->order->materials()
                ->wherePivotIn('status', ['ALLOCATED', 'RECEIVED'])
                ->exists();

            $hasRequestedMaterial = $this->order->materials()
                ->wherePivot('status', 'REQUESTED')
                ->exists();

            // Jika ada material yang sudah dialokasikan DAN tidak ada yang masih REQUESTED,
            // maka material sudah siap → langsung ke produksi (tidak perlu belanja lagi)
            $isMaterialReady = $hasAllocatedMaterial && !$hasRequestedMaterial;

            if ($this->perlu_belanja && !$isMaterialReady) {
                // Jalur belanja: Tahan di Rak Tunggu Belanja untuk Pengajuan Belanja Gabungan (Batch)
                $this->order->update([
                    'current_location' => 'Rak Tunggu Belanja',
                ]);

                $this->order->logs()->create([
                    'user_id' => Auth::id(),
                    'step' => 'SORTIR_BELANJA',
                    'action' => 'HELD_FOR_MATERIALS',
                    'description' => "Klasifikasi Sortir disimpan. SPK ditahan di Rak Tunggu Belanja untuk diajukan secara Batch/Gabungan.",
                ]);

                return redirect()->route('sortir.index')->with('info', "Klasifikasi OK! SPK ditahan di Rak Tunggu Belanja. Silakan lakukan Pengajuan Belanja Gabungan dari Tab Waiting Belanja.");
            }

            // 4. Routing: Material Siap atau Perlu Belanja = False -> Keep status as SORTIR, ready for Surat Jalan
            $this->order->update([
                'current_location' => 'Sortir (Siap Handover)',
            ]);

            $belanjaNote = $isMaterialReady
                ? 'Material sudah ALLOCATED (override)'
                : ($this->perlu_belanja ? 'Ya (override: material siap)' : 'Tidak');

            $this->order->logs()->create([
                'user_id' => Auth::id(),
                'step' => 'SORTIR',
                'action' => 'CLASSIFICATION_COMPLETED',
                'description' => "Klasifikasi Sortir Selesai: Bongkar=" . ($this->perlu_bongkar ? 'Ya' : 'Tidak') . ", Belanja={$belanjaNote}. SPK Pindah ke Produksi & Siap Surat Jalan.",
            ]);

            return redirect()->route('sortir.index')->with('success', 'Klasifikasi Selesai! SPK #' . $this->order->spk_number . ' telah selesai di Sortir & siap diserah-terimakan via Surat Jalan.');

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal memproses: ' . $e->getMessage()]);
        }
    }

    public function bypassSortir()
    {
        if (!in_array(Auth::user()->role, ['admin', 'owner', 'production_manager'])) {
            abort(403, 'Unauthorized action.');
        }

        // Mandatory reason enforcement (FR-11.1)
        $this->validate([
            'bypass_reason' => 'required|string|min:5',
        ], [
            'bypass_reason.required' => 'Alasan bypass wajib diisi!',
            'bypass_reason.min' => 'Alasan bypass minimal 5 karakter.',
        ]);

        try {
            $oldStatus = $this->order->status;
            DB::transaction(function () use ($oldStatus) {
                $this->order->status = WorkOrderStatus::PRODUCTION;
                $this->order->current_location = 'Rumah Abu';
                $this->order->save();

                $this->order->logs()->create([
                    'user_id' => Auth::id(),
                    'step' => 'SORTIR_BYPASS',
                    'action' => 'BYPASS_SERVIS',
                    'description' => "Bypass Servis ke Produksi. Alasan: " . $this->bypass_reason,
                ]);

                \App\Events\WorkOrderStatusUpdated::dispatch(
                    $this->order, 
                    $oldStatus, 
                    WorkOrderStatus::PRODUCTION, 
                    'Direct to Production (Bypass Livewire: ' . $this->bypass_reason . ')', 
                    Auth::id()
                );
            });

            return redirect()->route('sortir.index')->with('success', 'Order dikirim langsung ke Production dengan alasan audit trail!');
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getStockAvailabilityProperty()
    {
        $total = count($this->selectedMaterials);
        if ($total == 0) return 100;

        $allocated = collect($this->selectedMaterials)->where('status', 'ALLOCATED')->count();
        return round(($allocated / $total) * 100, 1);
    }

    public function render()
    {
        $materialQuery = Material::query();
        if ($this->searchMaterial) {
            $materialQuery->where('name', 'like', "%{$this->searchMaterial}%");
        }

        $solMaterials = (clone $materialQuery)->where(function($q) {
            $q->where('type', 'Material Sol')
              ->orWhere('category', 'SOL');
        })->orderBy('name')->get();

        $upperMaterials = (clone $materialQuery)->where(function($q) {
            $q->where('type', 'Material Upper')
              ->orWhere('category', 'UPPER');
        })->orderBy('name')->get();

        $otherMaterials = (clone $materialQuery)->where(function($q) {
            $q->whereNotIn('type', ['Material Sol', 'Material Upper'])
              ->orWhere('category', 'SHOPPING');
        })->orderBy('name')->get();

        $techSol = User::where('role', 'pic')->get();
        $techUpper = User::where('role', 'pic')->get();
        $services = \App\Models\Service::orderBy('name')->get();

        // Bongkar technician lists
        $bongkarSolTechs = User::whereIn('specialization', ['Sol Repair', 'PIC Material Sol'])->select('id', 'name')->get();
        $bongkarUpperTechs = User::whereIn('specialization', ['Upper Repair', 'Repaint', 'Jahit', 'PIC Material Upper'])->select('id', 'name')->get();

        return view('livewire.sortir.detail', [
            'solMaterials' => $solMaterials,
            'upperMaterials' => $upperMaterials,
            'otherMaterials' => $otherMaterials,
            'techSol' => $techSol,
            'techUpper' => $techUpper,
            'services' => $services,
            'bongkarSolTechs' => $bongkarSolTechs,
            'bongkarUpperTechs' => $bongkarUpperTechs,
        ])->layout('layouts.workshop-pwa');
    }

    public function requestMaterial(MaterialManagementService $service)
    {
        try {
            $request = $service->requestMissingMaterialsForWorkOrder($this->order);

            if ($request) {
                // Send purchase request to Finlog API (FR-4.3)
                $finlogService = app(\App\Services\FinlogApiService::class);
                $result = $finlogService->sendPurchaseRequest($request);

                $msg = "Request #{$request->request_number} berhasil dibuat & dikirim ke Finlog.";
                if (!empty($result['finlog_request_id'])) {
                    $msg .= " (Finlog ID: {$result['finlog_request_id']})";
                }

                $this->dispatch('notify', [
                    'type' => 'success', 
                    'message' => $msg
                ]);
                $this->loadOrder();
                $this->initializeState();
            } else {
                $this->dispatch('notify', ['type' => 'info', 'message' => "Tidak ada material yang perlu direquest."]);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => "Gagal: " . $e->getMessage()]);
        }
    }
}
