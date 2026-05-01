<?php

namespace App\Livewire\Warehouse\Disbursement;

use App\Models\WarehouseDisbursement;
use App\Models\Material;
use App\Traits\TracksWarehouseStock;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination, TracksWarehouseStock;

    public $search = '';

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function completeDisbursement($id)
    {
        $disbursement = WarehouseDisbursement::with('items')->findOrFail($id);
        
        if ($disbursement->status === 'COMPLETED') {
            return;
        }

        // Validate stock before completing
        foreach ($disbursement->items as $item) {
            $material = Material::find($item->material_id);
            if (!$material || $material->stock < $item->quantity) {
                session()->flash('error', "Gagal! Stok material '{$material->name}' tidak mencukupi untuk menyelesaikan transaksi ini.");
                return;
            }
        }

        DB::transaction(function () use ($disbursement) {
            $disbursement->update(['status' => 'COMPLETED']);

            foreach ($disbursement->items as $item) {
                $material = Material::find($item->material_id);
                $this->recordStockTransaction(
                    $material,
                    $item->quantity,
                    'OUT',
                    'WarehouseDisbursement',
                    $disbursement->id,
                    "Barang Keluar - SPK: {$item->spk_number} (Auto-Complete dari Daftar)"
                );
            }
        });

        session()->flash('message', "Barang Keluar {$disbursement->disbursement_number} telah SELESAI. Stok berhasil dikurangi.");
    }

    public function render()
    {
        $disbursements = WarehouseDisbursement::with(['user', 'items.material'])
            ->when($this->search, function ($query) {
                $query->where('disbursement_number', 'like', '%' . $this->search . '%')
                    ->orWhere('external_reference', 'like', '%' . $this->search . '%')
                    ->orWhereHas('items', function ($q) {
                        $q->where('spk_number', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.warehouse.disbursement.index', [
            'disbursements' => $disbursements
        ])->layout('layouts.app');
    }
}
