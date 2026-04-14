<?php

namespace App\Livewire\Procurement;

use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\WorkOrder;
use App\Services\MaterialManagementService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public $type = 'SHOPPING';
    public $notes = '';
    public $workOrderId = null;
    public $searchMaterial = '';
    public $selectedItems = [];
    public $selectedSpks = []; // Array of SPK IDs

    // Search results / Lists
    public $materialResults = [];
    public $orderResults = [];
    public $pendingOrders = []; // List of SPKs needing materials

    public function mount()
    {
        Gate::authorize('manageInventory', WorkOrder::class);
        $this->loadPendingOrders();
    }

    public function updatedType()
    {
        if ($this->type === 'PRODUCTION_PO') {
            $this->loadPendingOrders();
        }
        
        // Clear selections when switching type to avoid confusion
        $this->selectedItems = [];
        $this->selectedSpks = [];
    }

    public function loadPendingOrders()
    {
        // Get SPKs that are in SORTIR status and have missing materials (REQUESTED)
        // using the scope we defined earlier
        $this->pendingOrders = WorkOrder::waitingForMaterials()
            ->with(['materials' => function($q) {
                $q->where('work_order_materials.status', 'REQUESTED');
            }])
            ->whereDoesntHave('materialRequests', function($q) {
                $q->whereIn('status', ['PENDING', 'APPROVED', 'PURCHASED']);
            })
            ->latest()
            ->get();
    }

    public function toggleSpk($orderId)
    {
        if (in_array($orderId, $this->selectedSpks)) {
            $this->selectedSpks = array_diff($this->selectedSpks, [$orderId]);
        } else {
            $this->selectedSpks[] = $orderId;
        }

        $this->rebuildSelectedItems();
    }

    protected function rebuildSelectedItems()
    {
        $this->selectedItems = [];
        $tempItems = []; // To aggregate quantities if needed

        foreach ($this->selectedSpks as $spkId) {
            $order = WorkOrder::find($spkId);
            if (!$order) continue;

            $missingMaterials = $order->materials()
                ->wherePivot('status', 'REQUESTED')
                ->get();

            foreach ($missingMaterials as $mat) {
                $key = $mat->id;
                if (!isset($tempItems[$key])) {
                    $tempItems[$key] = [
                        'material_id' => $mat->id,
                        'name' => $mat->name,
                        'unit' => $mat->unit ?? 'pcs',
                        'quantity' => 0,
                        'price' => $mat->price ?? 0,
                        'orders' => []
                    ];
                }
                $tempItems[$key]['quantity'] += $mat->pivot->quantity;
                $tempItems[$key]['orders'][] = $order->spk_number;
            }
        }

        $this->selectedItems = array_values($tempItems);
    }

    public function updatedSearchMaterial()
    {
        if (strlen($this->searchMaterial) < 2) {
            $this->materialResults = [];
            return;
        }

        $this->materialResults = Material::where('name', 'like', '%' . $this->searchMaterial . '%')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function updatedSearchOrder()
    {
        if (strlen($this->searchOrder) < 2) {
            $this->orderResults = [];
            return;
        }

        $this->orderResults = WorkOrder::where('spk_number', 'like', '%' . $this->searchOrder . '%')
            ->orWhere('customer_name', 'like', '%' . $this->searchOrder . '%')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function selectOrder($id, $spkNumber)
    {
        $this->workOrderId = $id;
        $this->searchOrder = $spkNumber;
        $this->orderResults = [];
    }

    public function addItem($materialId)
    {
        $material = Material::find($materialId);
        if (!$material) return;

        // Check if already in list
        foreach ($this->selectedItems as $item) {
            if ($item['material_id'] == $materialId) {
                $this->dispatch('notify', type: 'warning', message: 'Material sudah ada di daftar.');
                return;
            }
        }

        $this->selectedItems[] = [
            'material_id' => $material->id,
            'name' => $material->name,
            'unit' => $material->unit ?? 'pcs',
            'quantity' => 1,
            'price' => $material->price ?? 0,
        ];

        $this->searchMaterial = '';
        $this->materialResults = [];
    }

    public function removeItem($index)
    {
        unset($this->selectedItems[$index]);
        $this->selectedItems = array_values($this->selectedItems);
    }

    public function submit()
    {
        Gate::authorize('manageInventory', WorkOrder::class);

        if (empty($this->selectedItems)) {
            $this->dispatch('notify', type: 'error', message: 'Pilih minimal satu material.');
            return;
        }

        $service = app(MaterialManagementService::class);

        try {
            if ($this->type === 'SHOPPING') {
                $items = [];
                foreach ($this->selectedItems as $item) {
                    $items[] = [
                        'material' => Material::find($item['material_id']),
                        'quantity' => $item['quantity'],
                    ];
                }
                $request = $service->createShoppingRequest($items, $this->workOrderId, null, $this->notes);
                
                session()->flash('success', "Request {$request->request_number} berhasil dibuat.");
                return redirect()->route('material-requests.show', $request->id);
            } else {
                // Production PO - Consolidated Mode
                if (empty($this->selectedSpks)) {
                    $this->dispatch('notify', type: 'error', message: 'Pilih minimal satu SPK.');
                    return;
                }

                // 1. Gather all items for all selected SPKs (and re-verify availability)
                $allFormattedItems = [];
                $validSpkCount = 0;

                foreach ($this->selectedSpks as $spkId) {
                    $order = WorkOrder::find($spkId);
                    if (!$order) continue;

                    // RE-VERIFY: Check if this SPK already has a pending/purchased REQ (concurrency safety)
                    $hasExisting = $order->materialRequests()
                        ->whereIn('status', ['PENDING', 'APPROVED', 'PURCHASED'])
                        ->exists();
                    
                    if ($hasExisting) continue;

                    $missingMaterials = $order->materials()
                        ->wherePivot('status', 'REQUESTED')
                        ->get();

                    if ($missingMaterials->isEmpty()) continue;

                    $validSpkCount++;
                    foreach ($missingMaterials as $mat) {
                        $allFormattedItems[] = [
                            'material' => $mat,
                            'work_order_id' => $order->id,
                            'shortage' => $mat->pivot->quantity,
                            'available_stock' => $mat->stock,
                        ];
                    }
                }

                if (empty($allFormattedItems)) {
                    $this->dispatch('notify', type: 'error', message: 'SPK yang Anda pilih sudah diproses oleh orang lain atau tidak butuh material.');
                    $this->loadPendingOrders(); // Refresh list
                    return;
                }

                // 2. Create ONE consolidated request
                $request = $service->createProductionPO(
                    $allFormattedItems, 
                    null, 
                    null, 
                    $this->notes ?: "Pengajuan gabungan untuk " . $validSpkCount . " SPK."
                );

                session()->flash('success', "Pengajuan Gabungan {$request->request_number} berhasil dibuat.");
                return redirect()->route('material-requests.show', $request->id);
            }

        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal membuat request: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.procurement.create')->layout('layouts.app');
    }
}
