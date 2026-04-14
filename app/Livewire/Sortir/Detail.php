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
        $this->order = WorkOrder::with(['customer', 'materials', 'services', 'logs'])->findOrFail($this->orderId);
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

        // Suggested tab logic
        $hasSolService = $this->order->services->contains(function($service) {
            $cat = strtolower($service->category);
            return str_contains($cat, 'sol') || str_contains($cat, 'midsole') || str_contains($cat, 'paket');
        });
        $this->activeTab = $hasSolService ? 'sol' : 'upper';
    }

    public function runSelfHealing()
    {
        $materialService = app(MaterialManagementService::class);
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

    public function completeSortir()
    {
        $this->validate([
            'pic_sortir_sol_id' => 'nullable|exists:users,id',
            'pic_sortir_upper_id' => 'nullable|exists:users,id',
        ]);

        try {
            $this->order->update([
                'pic_sortir_sol_id' => $this->pic_sortir_sol_id,
                'pic_sortir_upper_id' => $this->pic_sortir_upper_id,
            ]);

            $workflow = app(WorkflowService::class);

            if ($this->order->is_revising && $this->order->previous_status instanceof WorkOrderStatus) {
                $targetStatus = $this->order->previous_status;
                $workflow->updateStatus($this->order, $targetStatus, "Revision completed in Sortir (Livewire). Returning to " . $targetStatus->value);
                $this->order->update(['is_revising' => false, 'previous_status' => null]);
                return redirect()->route('sortir.index')->with('success', 'Revisi selesai. Sepatu kembali ke ' . $targetStatus->value);
            } 

            $workflow->updateStatus($this->order, WorkOrderStatus::PRODUCTION, 'Material Verified (Livewire). Ready for Production.');
            return redirect()->route('sortir.index')->with('success', 'Material OK. Sepatu masuk Production.');

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function bypassSortir()
    {
        if (!in_array(Auth::user()->role, ['admin', 'owner', 'production_manager'])) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $oldStatus = $this->order->status;
            DB::transaction(function () use ($oldStatus) {
                $this->order->status = WorkOrderStatus::PRODUCTION;
                $this->order->current_location = 'Rumah Abu';
                $this->order->save();

                \App\Events\WorkOrderStatusUpdated::dispatch(
                    $this->order, 
                    $oldStatus, 
                    WorkOrderStatus::PRODUCTION, 
                    'Direct to Production (Bypass Livewire)', 
                    Auth::id()
                );
            });

            return redirect()->route('sortir.index')->with('success', 'Order dikirim langsung ke Production!');
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

        $solMaterials = (clone $materialQuery)->where('category', 'PRODUCTION')->where('type', 'Material Sol')->orderBy('name')->get();
        $upperMaterials = (clone $materialQuery)->where('category', 'PRODUCTION')->where('type', 'Material Upper')->orderBy('name')->get();
        $otherMaterials = (clone $materialQuery)->where('category', 'SHOPPING')
            ->orWhere(function($q) use ($materialQuery) {
                $q->where('category', 'PRODUCTION')->whereNotIn('type', ['Material Sol', 'Material Upper']);
                if ($this->searchMaterial) $q->where('name', 'like', "%{$this->searchMaterial}%");
            })->orderBy('name')->get();

        $techSol = User::where('role', 'pic')->get();
        $techUpper = User::where('role', 'pic')->get();
        $services = \App\Models\Service::orderBy('name')->get();

        return view('livewire.sortir.detail', [
            'solMaterials' => $solMaterials,
            'upperMaterials' => $upperMaterials,
            'otherMaterials' => $otherMaterials,
            'techSol' => $techSol,
            'techUpper' => $techUpper,
            'services' => $services,
        ])->layout('layouts.app');
    }

    public function requestMaterial(MaterialManagementService $service)
    {
        try {
            $request = $service->requestMissingMaterialsForWorkOrder($this->order);

            if ($request) {
                $this->dispatch('notify', [
                    'type' => 'success', 
                    'message' => "Request #{$request->request_number} berhasil dibuat & dikirim ke Purchasing."
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
