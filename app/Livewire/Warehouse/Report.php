<?php

namespace App\Livewire\Warehouse;

use App\Models\Material;
use App\Models\MaterialTransaction;
use App\Models\WarehousePurchase;
use App\Models\WarehouseDisbursement;
use Livewire\Component;

class Report extends Component
{
    public $tab = 'purchase'; // purchase, disbursement, mutation
    public $startDate;
    public $endDate;
    
    // Filters for specific tabs
    public $purchaseStatus = '';
    public $disbursementStatus = '';
    public $materialId = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    public function render()
    {
        $data = [];
        $summary = [];

        if ($this->tab === 'purchase') {
            $query = WarehousePurchase::with(['items', 'user'])
                ->whereBetween('purchase_date', [$this->startDate, $this->endDate])
                ->when($this->purchaseStatus, function($q) {
                    $q->where('status', $this->purchaseStatus);
                })
                ->latest();
            
            $data = $query->get();
            $summary = [
                'total_amount' => $data->sum('total_amount'),
                'total_transactions' => $data->count(),
                'total_items' => $data->sum(fn($p) => $p->items->count())
            ];
        } elseif ($this->tab === 'disbursement') {
            $query = WarehouseDisbursement::with(['items', 'user'])
                ->whereBetween('disbursement_date', [$this->startDate, $this->endDate])
                ->latest();
            
            $data = $query->get();
            $summary = [
                'total_transactions' => $data->count(),
                'total_qty_out' => $data->sum(fn($d) => $d->items->sum('quantity')),
                'total_estimated_value' => $data->sum('total_amount')
            ];
        } elseif ($this->tab === 'mutation') {
            $query = MaterialTransaction::with(['material', 'user'])
                ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
                ->when($this->materialId, function($q) {
                    $q->where('material_id', $this->materialId);
                })
                ->latest();
            
            $data = $query->get();
            $summary = [
                'total_mutations' => $data->count(),
                'total_in' => $data->where('type', 'IN')->sum('quantity'),
                'total_out' => $data->where('type', 'OUT')->sum('quantity')
            ];
        }

        return view('livewire.warehouse.report', [
            'data' => $data,
            'summary' => $summary,
            'materials' => Material::orderBy('name')->get()
        ])->layout('layouts.app');
    }
}
