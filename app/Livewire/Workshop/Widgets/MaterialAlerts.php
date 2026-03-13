<?php

namespace App\Livewire\Workshop\Widgets;

use Livewire\Component;
use App\Models\Material;

class MaterialAlerts extends Component
{
    public $lowStockMaterials;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->lowStockMaterials = Material::whereRaw('stock < min_stock')
            ->orderBy('stock', 'asc')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.workshop.widgets.material-alerts');
    }
}
