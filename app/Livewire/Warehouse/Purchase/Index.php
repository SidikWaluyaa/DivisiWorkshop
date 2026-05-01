<?php

namespace App\Livewire\Warehouse\Purchase;

use App\Models\WarehousePurchase;
use App\Models\Material;
use App\Traits\TracksWarehouseStock;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination, TracksWarehouseStock;

    public $search = '';
    public $purchaseType = '';

    protected $updatesQueryString = ['search', 'purchaseType'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function completePurchase($id)
    {
        $purchase = WarehousePurchase::with('items')->findOrFail($id);
        
        if ($purchase->status === 'COMPLETED') {
            return;
        }

        DB::transaction(function () use ($purchase) {
            $purchase->update(['status' => 'COMPLETED']);

            foreach ($purchase->items as $item) {
                $material = Material::find($item->material_id);
                if ($material) {
                    $this->recordStockTransaction(
                        $material,
                        $item->quantity,
                        'IN',
                        'WarehousePurchase',
                        $purchase->id,
                        "Belanja {$purchase->purchase_type} - SPK: {$item->spk_number} (Auto-Complete dari Daftar)"
                    );
                }
            }
        });

        session()->flash('message', "Belanja {$purchase->purchase_number} telah SELESAI. Stok berhasil ditambahkan.");
    }

    public function render()
    {
        $purchases = WarehousePurchase::with('items.material')
            ->when($this->search, function ($query) {
                $query->where('purchase_number', 'like', '%' . $this->search . '%')
                    ->orWhere('external_reference', 'like', '%' . $this->search . '%')
                    ->orWhereHas('items', function($q) {
                        $q->where('spk_number', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->purchaseType, function ($query) {
                $query->where('purchase_type', $this->purchaseType);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.warehouse.purchase.index', [
            'purchases' => $purchases
        ])->layout('layouts.app');
    }
}
