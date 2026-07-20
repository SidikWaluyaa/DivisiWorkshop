<?php

namespace App\Livewire\Warehouse\Disbursement;

use App\Models\Material;
use App\Models\WarehouseDisbursement;
use App\Models\WorkOrder;
use App\Traits\TracksWarehouseStock;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Form extends Component
{
    use TracksWarehouseStock;

    public $disbursementId;
    public $disbursement_number;
    public $external_reference;
    public $status = 'COMPLETED'; 
    public $disbursement_date;
    public $notes;
    
    public $spkGroups = [];

    public $showMaterialModal = false;
    public $targetGroupIndex = null;
    public $checklistSearch = '';
    public $selectedChecklist = [];

    public $allSpks = [];

    protected $rules = [
        'disbursement_date' => 'required|date',
        'status' => 'required|in:PENDING,COMPLETED,CANCELLED',
        'spkGroups.*.spk_number' => 'nullable|string',
        'spkGroups.*.items.*.material_id' => 'required|exists:materials,id',
        'spkGroups.*.items.*.quantity' => 'required|integer|min:1',
    ];

    public function mount($disbursementId = null)
    {
        $this->disbursement_date = now()->format('Y-m-d');
        $this->allSpks = WorkOrder::latest()->take(100)->pluck('spk_number')->toArray();

        if ($disbursementId) {
            $disbursement = WarehouseDisbursement::with('items.material')->findOrFail($disbursementId);
            $this->disbursementId = $disbursement->id;
            $this->disbursement_number = $disbursement->disbursement_number;
            $this->external_reference = $disbursement->external_reference;
            $this->status = $disbursement->status;
            $this->disbursement_date = $disbursement->disbursement_date->format('Y-m-d');
            $this->notes = $disbursement->notes;
            
            $groupedItems = $disbursement->items->groupBy('spk_number');
            foreach ($groupedItems as $spk => $items) {
                $groupItems = [];
                foreach ($items as $item) {
                    $groupItems[] = [
                        'material_id' => $item->material_id,
                        'material_name' => $item->material->name ?? '',
                        'material_type' => $item->material->type ?? '',
                        'material_size' => $item->material->size ?? '',
                        'material_stock' => $item->material->stock ?? 0,
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
            $this->generateDisbursementNumber();
            $this->addSpkGroup();
        }
    }

    public function generateDisbursementNumber()
    {
        $today = now()->format('Ymd');
        $count = WarehouseDisbursement::whereDate('created_at', now()->toDateString())->count() + 1;
        $this->disbursement_number = "WH-OUT-{$today}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
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
            'material_type' => $material ? $material->type : '',
            'material_size' => $material ? $material->size : '',
            'material_stock' => $material ? $material->stock : 0,
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

        if ($this->status === 'COMPLETED') {
            foreach ($this->spkGroups as $gIndex => $group) {
                foreach ($group['items'] as $iIndex => $itemData) {
                    $material = Material::find($itemData['material_id']);
                    if (!$material || $material->stock < $itemData['quantity']) {
                        $this->addError("spkGroups.{$gIndex}.items.{$iIndex}.quantity", "Stok tidak mencukupi.");
                        return;
                    }
                }
            }
        }

        DB::transaction(function () {
            $totalAmount = 0;
            foreach ($this->spkGroups as $group) {
                foreach ($group['items'] as $item) {
                    $totalAmount += $item['quantity'] * ($item['price'] ?? 0);
                }
            }

            $oldDisbursement = $this->disbursementId ? WarehouseDisbursement::with('items')->find($this->disbursementId) : null;
            $oldStatus = $oldDisbursement ? $oldDisbursement->status : 'PENDING';

            // 1. REVERT OLD STOCK if it was COMPLETED
            if ($oldStatus === 'COMPLETED') {
                foreach ($oldDisbursement->items as $oldItem) {
                    $material = Material::find($oldItem->material_id);
                    if ($material) {
                        $this->recordStockTransaction(
                            $material,
                            $oldItem->quantity,
                            'IN', // Reverse of OUT is IN
                            'WarehouseDisbursement',
                            $oldDisbursement->id,
                            "Koreksi Barang Keluar (Revert) - {$oldDisbursement->disbursement_number}"
                        );
                    }
                }
            }

            $disbursement = WarehouseDisbursement::updateOrCreate(
                ['id' => $this->disbursementId],
                [
                    'disbursement_number' => $this->disbursement_number,
                    'external_reference' => $this->external_reference,
                    'status' => $this->status,
                    'disbursement_date' => $this->disbursement_date,
                    'total_amount' => $totalAmount,
                    'notes' => $this->notes,
                    'user_id' => auth()->id() ?? 1,
                ]
            );

            if ($this->disbursementId) {
                $disbursement->items()->delete();
            }

            foreach ($this->spkGroups as $group) {
                foreach ($group['items'] as $itemData) {
                    $disbursement->items()->create([
                        'material_id' => $itemData['material_id'],
                        'spk_number' => $group['spk_number'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'] ?? 0,
                        'subtotal' => ($itemData['quantity'] * ($itemData['price'] ?? 0)),
                    ]);

                    // 2. APPLY NEW STOCK if current status is COMPLETED
                    if ($this->status === 'COMPLETED') {
                        $material = Material::find($itemData['material_id']);
                        $this->recordStockTransaction(
                            $material,
                            $itemData['quantity'],
                            'OUT',
                            'WarehouseDisbursement',
                            $disbursement->id,
                            "Barang Keluar - SPK: {$group['spk_number']}"
                        );
                    }
                }
            }
        });

        session()->flash('message', 'Data Barang Keluar berhasil disimpan.');
        return redirect()->route('storage.disbursement.index');
    }

    public function getFilteredMaterialsProperty()
    {
        return Material::query()
            ->when($this->checklistSearch, function($q) {
                $term = '%' . $this->checklistSearch . '%';
                $q->where(function($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('type', 'like', $term)
                        ->orWhere('size', 'like', $term);
                });
            })
            ->orderBy('name', 'asc')
            ->limit(100)
            ->get();
    }

    public function render()
    {
        return view('livewire.warehouse.disbursement.form', [
            'modalMaterials' => $this->filteredMaterials
        ])->layout('layouts.app');
    }
}
