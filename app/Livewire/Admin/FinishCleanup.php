<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinishCleanup extends Component
{
    use WithPagination;

    public $search = '';
    public $filterMonths = '';
    public $selectedIds = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterMonths' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatedFilterMonths()
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->getWorkOrdersQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds()
    {
        $this->selectAll = count($this->selectedIds) === $this->getWorkOrdersQuery()->count();
    }

    private function getWorkOrdersQuery()
    {
        $query = WorkOrder::where('status', WorkOrderStatus::SELESAI->value)
                    ->whereNull('taken_date');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterMonths) {
            $query->where('finished_date', '<=', now()->subMonths((int)$this->filterMonths));
        }

        return $query->orderBy('finished_date', 'asc');
    }

    public function cleanupSelected()
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('swal:alert', [
                'icon' => 'warning',
                'title' => 'Peringatan',
                'text' => 'Silakan pilih data yang ingin dibersihkan terlebih dahulu.'
            ]);
            return;
        }

        $count = count($this->selectedIds);

        DB::beginTransaction();
        try {
            $orders = WorkOrder::whereIn('id', $this->selectedIds)->get();
            
            foreach ($orders as $order) {
                // Log the manual cleanup
                $order->logs()->create([
                    'step' => WorkOrderStatus::SELESAI->value,
                    'action' => 'MANUAL_CLEANUP',
                    'user_id' => Auth::id(),
                    'description' => 'Data dibersihkan secara massal via Cleanup Hub karena fisik tidak ditemukan.'
                ]);
                
                $order->delete(); // Soft Delete
            }

            DB::commit();

            $this->selectedIds = [];
            $this->selectAll = false;

            $this->dispatch('swal:alert', [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => "Berhasil memindahkan {$count} data ke Sampah."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        $workOrders = $this->getWorkOrdersQuery()->paginate(50);

        return view('livewire.admin.finish-cleanup', [
            'workOrders' => $workOrders
        ])->layout('layouts.app');
    }
}
