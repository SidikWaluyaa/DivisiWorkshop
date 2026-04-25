<?php

namespace App\Livewire\Admin;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $sortBy = 'latest';
    public $perPage = 10;
    public $selected = [];
    public $selectedAll = false;

    public function updatedSelectedAll($value)
    {
        if ($value) {
            $this->selected = Service::query()
                ->when($this->search, function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                })
                ->when($this->category, fn($q) => $q->where('category', $this->category))
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'category', 'minPrice', 'maxPrice', 'sortBy']);
        $this->resetPage();
    }

    public function deleteSelected($ids = null)
    {
        $targetIds = $ids ?: $this->selected;
        if (empty($targetIds)) return;

        $protectedNames = ['custom', 'custom service', 'custom services', 'lainnya', 'other'];
        
        $services = Service::whereIn('id', $targetIds)->get();
        $deletableIds = [];
        $protectedCount = 0;

        foreach($services as $service) {
            if (in_array(strtolower($service->name), $protectedNames)) {
                $protectedCount++;
                continue;
            }
            $deletableIds[] = $service->id;
        }

        if (count($deletableIds) > 0) {
            Service::whereIn('id', $deletableIds)->delete();
            $this->dispatch('notify', ['type' => 'success', 'message' => count($deletableIds) . ' layanan berhasil dihapus.']);
        }
        
        if ($protectedCount > 0) {
             $this->dispatch('notify', ['type' => 'warning', 'message' => "$protectedCount layanan diproteksi (tidak bisa dihapus)."]);
        }

        $this->selected = array_diff($this->selected, $targetIds);
    }

    public function render()
    {
        $query = Service::query();

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Category
        if ($this->category) {
            $query->where('category', $this->category);
        }

        // Price Range
        if ($this->minPrice) {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice) {
            $query->where('price', '<=', $this->maxPrice);
        }

        // Sorting
        switch ($this->sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $services = $query->paginate($this->perPage);
        $categories = Service::distinct()->pluck('category')->filter()->values();

        return view('livewire.admin.service-index', [
            'services' => $services,
            'categories' => $categories
        ]);
    }
}
