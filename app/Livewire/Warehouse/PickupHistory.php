<?php

namespace App\Livewire\Warehouse;

use App\Models\WorkOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class PickupHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $startDate;
    public $endDate;
    public $sort = 'desc';
    public $pickup_method_filter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'pickup_method_filter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->startDate = request()->query('startDate', '');
        $this->endDate = request()->query('endDate', '');
        $this->pickup_method_filter = request()->query('pickup_method_filter', '');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPickupMethodFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'startDate', 'endDate', 'pickup_method_filter']);
        $this->resetPage();
    }

    public function getStatsProperty()
    {
        return [
            'today' => WorkOrder::whereDate('taken_date', Carbon::today())->count(),
            'week' => WorkOrder::whereBetween('taken_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'month' => WorkOrder::whereMonth('taken_date', Carbon::now()->month)
                                ->whereYear('taken_date', Carbon::now()->year)
                                ->count(),
            'total' => WorkOrder::whereNotNull('taken_date')->count(),
        ];
    }

    public function undoPickup($id)
    {
        try {
            $storageService = app(\App\Services\Storage\StorageService::class);
            $storageService->undoPickup($id);
            
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Pengambilan dibatalkan. Sepatu kembali ke status Menunggu Disimpan.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function updatePickupMethod($id, $method, $actualCost = null)
    {
        try {
            $order = WorkOrder::findOrFail($id);
            $updateData = ['pickup_method' => $method];
            
            if ($actualCost !== null) {
                $updateData['actual_shipping_cost'] = $actualCost;
            }

            $order->update($updateData);

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Tersimpan',
                'text' => 'Data pengambilan berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $query = WorkOrder::whereNotNull('taken_date')
            ->with(['spkCoverPhoto', 'workOrderServices.service']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('shoe_brand', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->startDate) {
            $query->whereDate('taken_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('taken_date', '<=', $this->endDate);
        }

        if ($this->pickup_method_filter) {
            if ($this->pickup_method_filter === 'offline') {
                $query->where(function($q) {
                    $q->where('pickup_method', 'like', 'Offline%')
                      ->orWhereNull('pickup_method')
                      ->orWhere('pickup_method', '');
                });
            } elseif ($this->pickup_method_filter === 'delivery') {
                $query->where('pickup_method', 'not like', 'Offline%')
                      ->whereNotNull('pickup_method')
                      ->where('pickup_method', '!=', '');
            }
        }

        $orders = $query->orderBy('taken_date', $this->sort)
                        ->paginate(20);

        return view('livewire.warehouse.pickup-history', [
            'orders' => $orders,
            'stats' => $this->stats
        ])->layout('layouts.app');
    }
}
