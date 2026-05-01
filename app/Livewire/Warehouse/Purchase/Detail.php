<?php

namespace App\Livewire\Warehouse\Purchase;

use App\Models\WarehousePurchase;
use Livewire\Component;

class Detail extends Component
{
    public $purchase;
    public $spkGroups = [];

    public function mount($purchaseId)
    {
        $this->purchase = WarehousePurchase::with(['items.material', 'user'])->findOrFail($purchaseId);
        
        $groupedItems = $this->purchase->items->groupBy('spk_number');
        foreach ($groupedItems as $spk => $items) {
            $this->spkGroups[] = [
                'spk_number' => $spk,
                'items' => $items
            ];
        }
    }

    public function render()
    {
        return view('livewire.warehouse.purchase.detail')->layout('layouts.app');
    }
}
