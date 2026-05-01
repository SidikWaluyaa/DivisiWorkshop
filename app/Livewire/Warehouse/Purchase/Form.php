<?php

namespace App\Livewire\Warehouse\Purchase;

use App\Models\Material;
use App\Models\WarehousePurchase;
use App\Models\WorkOrder;
use App\Traits\TracksWarehouseStock;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Form extends Component
{
    use TracksWarehouseStock;

    public $purchaseId;
    public $purchase_number;
    public $external_reference;
    public $purchase_type = 'Reguler';
    public $status = 'PENDING';
    public $purchase_date;
    public $notes;
    
    public $spkGroups = [];
    
    public $showMaterialModal = false;
    public $targetGroupIndex = null;
    public $checklistSearch = '';
    public $selectedChecklist = [];

    public $allSpks = [];

    protected $rules = [
        'purchase_type' => 'required|in:Reguler,Prioritas,Urgent',
        'status' => 'required|in:PENDING,PROCESSING,COMPLETED,CANCELLED',
        'purchase_date' => 'required|date',
        'spkGroups.*.spk_number' => 'nullable|string',
        'spkGroups.*.items.*.material_id' => 'required|exists:materials,id',
        'spkGroups.*.items.*.quantity' => 'required|integer|min:1',
        'spkGroups.*.items.*.price' => 'required|numeric|min:0',
    ];

    public function mount($purchaseId = null)
    {
        $this->purchase_date = now()->format('Y-m-d');
        $this->allSpks = WorkOrder::latest()->take(100)->pluck('spk_number')->toArray();

        if ($purchaseId) {
            $purchase = WarehousePurchase::with('items.material')->findOrFail($purchaseId);
            $this->purchaseId = $purchase->id;
            $this->purchase_number = $purchase->purchase_number;
            $this->external_reference = $purchase->external_reference;
            $this->purchase_type = $purchase->purchase_type;
            $this->status = $purchase->status;
            $this->purchase_date = $purchase->purchase_date->format('Y-m-d');
            $this->notes = $purchase->notes;
            
            $groupedItems = $purchase->items->groupBy('spk_number');
            foreach ($groupedItems as $spk => $items) {
                $groupItems = [];
                foreach ($items as $item) {
                    $groupItems[] = [
                        'material_id' => $item->material_id,
                        'material_name' => $item->material->name ?? '',
                        'material_stock' => $item->material->stock ?? 0, // ADDED STOCK
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }
                $this->spkGroups[] = [
                    'spk_number' => $spk,
                    'items' => $groupItems
                ];
            }
        } else {
            $this->generatePurchaseNumber();
            $this->addSpkGroup();
        }
    }

    public function generatePurchaseNumber()
    {
        $today = now()->format('Ymd');
        $count = WarehousePurchase::whereDate('created_at', now()->toDateString())->count() + 1;
        $this->purchase_number = "WH-IN-{$today}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function addSpkGroup()
    {
        $this->spkGroups[] = [
            'spk_number' => '',
            'items' => []
        ];
    }

    public function removeSpkGroup($index)
    {
        unset($this->spkGroups[$index]);
        $this->spkGroups = array_values($this->spkGroups);
    }

    public function addMaterialToGroup($groupIndex, $materialId = null)
    {
        $material = $materialId ? Material::find($materialId) : null;
        
        $this->spkGroups[$groupIndex]['items'][] = [
            'material_id' => $material ? $material->id : '',
            'material_name' => $material ? $material->name : '',
            'material_stock' => $material ? $material->stock : 0, // ADDED STOCK
            'quantity' => 1,
            'price' => $material ? $material->price : 0,
        ];
    }

    public function removeMaterialFromGroup($groupIndex, $itemIndex)
    {
        unset($this->spkGroups[$groupIndex]['items'][$itemIndex]);
        $this->spkGroups[$groupIndex]['items'] = array_values($this->spkGroups[$groupIndex]['items']);
    }

    public function openMaterialModal($groupIndex)
    {
        $this->targetGroupIndex = $groupIndex;
        $this->showMaterialModal = true;
        $this->checklistSearch = '';
        $this->selectedChecklist = [];
    }

    public function toggleChecklist($materialId)
    {
        if (in_array($materialId, $this->selectedChecklist)) {
            $this->selectedChecklist = array_diff($this->selectedChecklist, [$materialId]);
        } else {
            $this->selectedChecklist[] = $materialId;
        }
    }

    public function addFromChecklist()
    {
        $materials = Material::whereIn('id', $this->selectedChecklist)->get();
        foreach ($materials as $material) {
            $this->addMaterialToGroup($this->targetGroupIndex, $material->id);
        }

        $this->showMaterialModal = false;
        $this->selectedChecklist = [];
        $this->targetGroupIndex = null;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $totalAmount = 0;
            foreach ($this->spkGroups as $group) {
                foreach ($group['items'] as $item) {
                    $totalAmount += $item['quantity'] * $item['price'];
                }
            }

            $oldPurchase = $this->purchaseId ? WarehousePurchase::with('items')->find($this->purchaseId) : null;
            $oldStatus = $oldPurchase ? $oldPurchase->status : 'PENDING';

            // 1. REVERT OLD STOCK if it was COMPLETED
            if ($oldStatus === 'COMPLETED') {
                foreach ($oldPurchase->items as $oldItem) {
                    $material = Material::find($oldItem->material_id);
                    if ($material) {
                        $this->recordStockTransaction(
                            $material,
                            $oldItem->quantity,
                            'OUT', // Reverse of IN is OUT
                            'WarehousePurchase',
                            $oldPurchase->id,
                            "Koreksi Data Belanja (Revert) - {$oldPurchase->purchase_number}"
                        );
                    }
                }
            }

            $purchase = WarehousePurchase::updateOrCreate(
                ['id' => $this->purchaseId],
                [
                    'purchase_number' => $this->purchase_number,
                    'external_reference' => $this->external_reference,
                    'purchase_type' => $this->purchase_type,
                    'status' => $this->status,
                    'purchase_date' => $this->purchase_date,
                    'total_amount' => $totalAmount,
                    'notes' => $this->notes,
                    'user_id' => auth()->id() ?? 1,
                ]
            );

            if ($this->purchaseId) {
                $purchase->items()->delete();
            }

            foreach ($this->spkGroups as $group) {
                foreach ($group['items'] as $itemData) {
                    $purchase->items()->create([
                        'material_id' => $itemData['material_id'],
                        'spk_number' => $group['spk_number'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                        'subtotal' => $itemData['quantity'] * $itemData['price'],
                    ]);

                    // 2. APPLY NEW STOCK if current status is COMPLETED
                    if ($this->status === 'COMPLETED') {
                        $material = Material::find($itemData['material_id']);
                        $this->recordStockTransaction(
                            $material,
                            $itemData['quantity'],
                            'IN',
                            'WarehousePurchase',
                            $purchase->id,
                            "Belanja {$this->purchase_type} - SPK: {$group['spk_number']}"
                        );
                    }
                }
            }
        });

        session()->flash('message', 'Data Belanja berhasil disimpan.');
        return redirect()->route('storage.purchase.index');
    }

    public function getFilteredMaterialsProperty()
    {
        return Material::when($this->checklistSearch, function($q) {
            $q->where('name', 'like', '%' . $this->checklistSearch . '%');
        })
        ->limit(50)
        ->get();
    }

    public function render()
    {
        return view('livewire.warehouse.purchase.form', [
            'modalMaterials' => $this->filteredMaterials
        ])->layout('layouts.app');
    }
}
