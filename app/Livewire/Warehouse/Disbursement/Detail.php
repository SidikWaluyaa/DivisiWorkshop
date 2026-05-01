<?php

namespace App\Livewire\Warehouse\Disbursement;

use App\Models\WarehouseDisbursement;
use Livewire\Component;

class Detail extends Component
{
    public $disbursement;
    public $spkGroups = [];

    public function mount($disbursementId)
    {
        $this->disbursement = WarehouseDisbursement::with(['items.material', 'user'])->findOrFail($disbursementId);
        
        $groupedItems = $this->disbursement->items->groupBy('spk_number');
        foreach ($groupedItems as $spk => $items) {
            $this->spkGroups[] = [
                'spk_number' => $spk,
                'items' => $items
            ];
        }
    }

    public function render()
    {
        return view('livewire.warehouse.disbursement.detail')->layout('layouts.app');
    }
}
