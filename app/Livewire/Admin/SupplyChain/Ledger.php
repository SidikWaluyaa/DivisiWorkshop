<?php

namespace App\Livewire\Admin\SupplyChain;

use App\Models\Material;
use App\Models\MaterialTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Ledger extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $material_id = '';

    #[Url(history: true)]
    public $type = '';

    #[Url(history: true)]
    public $date_from = '';

    #[Url(history: true)]
    public $date_to = '';

    public function updating($property)
    {
        if (in_array($property, ['material_id', 'type', 'date_from', 'date_to'])) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset(['material_id', 'type', 'date_from', 'date_to']);
        $this->resetPage();
    }

    public function render()
    {
        $query = MaterialTransaction::with(['material', 'user']);

        if ($this->material_id) {
            $query->where('material_id', $this->material_id);
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->date_from) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        return view('livewire.admin.supply-chain.ledger', [
            'transactions' => $query->latest()->paginate(50),
            'materials' => Material::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
