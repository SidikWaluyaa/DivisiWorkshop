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

    #[Url(history: true)]
    public $shoeBrand = '';

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

    public function updatingShoeBrand()
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
                $q->where('step', 'WAREHOUSE_BEFORE');
            })
            ->whereHas('photos', function($q) {
                $q->where('step', 'FINISH');
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

        if ($this->shoeBrand) {
            $query->where('shoe_brand', 'LIKE', '%' . $this->shoeBrand . '%');
        }

        $workOrders = $query->paginate($this->perPage);
        $services = Service::orderBy('name')->get();
        
        $brands = $this->getCleanBrands();

        return view('livewire.cs.after-photo-gallery', [
            'workOrders' => $workOrders,
            'services' => $services,
            'brands' => $brands,
        ])->layout('layouts.app');
    }

    /**
     * Get a list of unique normalized brand keywords
     */
    public function getCleanBrands()
    {
        $rawBrands = \App\Models\WorkOrder::whereNotNull('shoe_brand')
            ->where('shoe_brand', '!=', '')
            ->distinct()
            ->pluck('shoe_brand');

        $cleanBrands = [];

        $knownBrands = [
            'Adidas', 'Nike', 'New Balance', 'Converse', 'Vans', 'Puma', 'Reebok', 
            'Asics', 'Jordan', 'Under Armour', 'Skechers', 'Fila', 'Mizuno', 
            'Timberland', 'Crocs', 'Salomon', 'Compass', 'Ventela', 'Aerostreet',
            'Docmart', 'Dr. Martens', 'Ortuseight', 'Specs', 'Diadora', 'Geoff Max',
            'Hoka', 'Brooks', 'Saucony', 'Balenciaga'
        ];

        foreach ($rawBrands as $raw) {
            $trimmed = trim($raw);
            $lowerTrimmed = strtolower($trimmed);
            $matched = false;

            if ($lowerTrimmed === 'nb' || str_starts_with($lowerTrimmed, 'nb ')) {
                $cleanBrands['New Balance'] = 'New Balance';
                continue;
            }

            foreach ($knownBrands as $known) {
                if (str_contains($lowerTrimmed, strtolower($known))) {
                    $cleanBrands[$known] = $known;
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                $words = explode(' ', $trimmed);
                $firstWord = ucfirst(strtolower($words[0]));
                if (strlen($firstWord) >= 3) {
                    $cleanBrands[$firstWord] = $firstWord;
                } else {
                    $cleanBrands[$trimmed] = $trimmed;
                }
            }
        }

        return collect($cleanBrands)->sort()->values()->all();
    }
}
