<?php

namespace App\Livewire\Cs;

use App\Models\WorkOrderPhoto;
use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AfterPhotoGallery extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $serviceId = '';

    public $perPage = 8;

    protected $listeners = ['load-more' => 'loadMore'];

    public function updatingSearch()
    {
        $this->resetPage();
        $this->perPage = 8;
    }

    public function updatingServiceId()
    {
        $this->resetPage();
        $this->perPage = 8;
    }

    public function loadMore()
    {
        $this->perPage += 8;
    }

    /**
     * Use placeholder for lazy loading the whole component
     */
    public function placeholder()
    {
        return view('livewire.cs.after-photo-gallery-skeleton');
    }

    public function render()
    {
        $query = \App\Models\WorkOrder::query()
            ->with(['customer', 'workOrderServices.service', 'photos' => function($q) {
                $q->whereIn('step', ['FINISH', 'WAREHOUSE_BEFORE']);
            }])
            ->where('status', \App\Enums\WorkOrderStatus::SELESAI->value)
            ->whereHas('photos', function($q) {
                $q->whereIn('step', ['FINISH', 'WAREHOUSE_BEFORE']);
            })
            ->latest();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'LIKE', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'LIKE', '%' . $this->search . '%')
                  ->orWhereHas('workOrderServices.service', function ($sub) {
                      $sub->where('name', 'LIKE', '%' . $this->search . '%');
                  })
                  ->orWhereHas('workOrderServices', function ($sub) {
                      $sub->where('custom_service_name', 'LIKE', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->serviceId) {
            $query->whereHas('workOrderServices', function ($q) {
                $q->where('service_id', $this->serviceId);
            });
        }

        $workOrders = $query->paginate($this->perPage);
        $services = Service::orderBy('name')->get();

        return view('livewire.cs.after-photo-gallery', [
            'workOrders' => $workOrders,
            'services' => $services,
        ])->layout('layouts.app');
    }
}
