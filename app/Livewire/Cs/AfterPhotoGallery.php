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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingServiceId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = WorkOrderPhoto::query()
            ->with(['workOrder.customer', 'workOrder.workOrderServices.service'])
            ->where('step', 'FINISH')
            ->latest();

        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('workOrder', function ($sub) {
                    $sub->where('spk_number', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('customer_name', 'LIKE', '%' . $this->search . '%');
                })
                ->orWhereHas('workOrder.workOrderServices.service', function ($sub) {
                    $sub->where('name', 'LIKE', '%' . $this->search . '%');
                })
                ->orWhereHas('workOrder.workOrderServices', function ($sub) {
                    $sub->where('custom_service_name', 'LIKE', '%' . $this->search . '%');
                });
            });
        }

        if ($this->serviceId) {
            $query->whereHas('workOrder.workOrderServices', function ($q) {
                $q->where('service_id', $this->serviceId);
            });
        }

        $photos = $query->paginate(20);
        $services = Service::orderBy('name')->get();

        return view('livewire.cs.after-photo-gallery', [
            'photos' => $photos,
            'services' => $services,
        ])->layout('layouts.app');
    }
}
