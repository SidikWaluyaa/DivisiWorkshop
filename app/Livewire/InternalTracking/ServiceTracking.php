<?php

namespace App\Livewire\InternalTracking;

use App\Models\WorkOrderService;
use App\Models\Service;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceTracking extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $category = '';

    #[Url]
    public $date_start = '';

    #[Url]
    public $date_end = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'date_start' => ['except' => ''],
        'date_end' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingDateStart()
    {
        $this->resetPage();
    }

    public function updatingDateEnd()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->category = '';
        $this->date_start = '';
        $this->date_end = '';
        $this->resetPage();
    }

    public function render()
    {
        // 1. Build Base Query for Filtering
        $query = WorkOrderService::query()
            ->with(['workOrder', 'service', 'technician']);

        if ($this->date_start) {
            $query->where('created_at', '>=', Carbon::parse($this->date_start)->startOfDay());
        }
        if ($this->date_end) {
            $query->where('created_at', '<=', Carbon::parse($this->date_end)->endOfDay());
        }

        if ($this->category) {
            $query->where(function($q) {
                $q->where('category_name', $this->category)
                  ->orWhereHas('service', function($sq) {
                      $sq->where('category', $this->category);
                  });
            });
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('custom_service_name', 'like', '%' . $this->search . '%')
                  ->orWhere('category_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('service', function($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('workOrder', function($wq) {
                      $wq->where('spk_number', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Clone query for metrics before paginating
        $metricsQuery = clone $query;

        // 2. Compute Aggregates
        $totalFrequency = $metricsQuery->count();
        $totalRevenue = (float) $metricsQuery->sum('cost');
        $avgCost = $totalFrequency > 0 ? ($totalRevenue / $totalFrequency) : 0;

        // 3. Paginated Results
        $servicesList = $query->latest('created_at')->paginate(15);

        // 4. Resolve unique categories dynamically
        $categoriesFromPivot = DB::table('work_order_services')
            ->whereNotNull('category_name')
            ->where('category_name', '!=', '')
            ->distinct()
            ->pluck('category_name')
            ->toArray();

        $categoriesFromService = DB::table('services')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->toArray();

        $categories = array_unique(array_filter(array_merge($categoriesFromPivot, $categoriesFromService)));
        sort($categories);

        return view('livewire.internal-tracking.service-tracking', [
            'servicesList' => $servicesList,
            'categories' => $categories,
            'metrics' => [
                'total_frequency' => $totalFrequency,
                'total_revenue' => $totalRevenue,
                'avg_cost' => $avgCost,
            ]
        ])->layout('layouts.app');
    }
}
