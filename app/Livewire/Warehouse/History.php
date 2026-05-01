<?php

namespace App\Livewire\Warehouse;

use App\Models\Material;
use App\Models\MaterialTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';
    public $materialId = '';
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingType() { $this->resetPage(); }
    public function updatingMaterialId() { $this->resetPage(); }
    public function updatingStartDate() { $this->resetPage(); }
    public function updatingEndDate() { $this->resetPage(); }

    public function render()
    {
        $transactions = MaterialTransaction::with(['material', 'user'])
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('notes', 'like', '%' . $this->search . '%')
                      ->orWhereHas('material', function($mq) {
                          $mq->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->when($this->materialId, function ($query) {
                $query->where('material_id', $this->materialId);
            })
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->latest()
            ->paginate(15);

        return view('livewire.warehouse.history', [
            'transactions' => $transactions,
            'materials' => Material::orderBy('name')->get()
        ])->layout('layouts.app');
    }
}
