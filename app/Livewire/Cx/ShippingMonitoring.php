<?php

namespace App\Livewire\Cx;

use App\Models\Shipping;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ShippingMonitoring extends Component
{
    use WithPagination;

    public $search = '';
    public $date_start;
    public $date_end;
    public $category = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'date_start' => ['except' => ''],
        'date_end' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    public function mount()
    {
        $this->date_start = '';
        $this->date_end = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function filterByPreset($type)
    {
        switch ($type) {
            case 'today':
                $this->date_start = Carbon::today()->toDateString();
                $this->date_end = Carbon::today()->toDateString();
                break;
            case 'week':
                $this->date_start = Carbon::today()->startOfWeek()->toDateString();
                $this->date_end = Carbon::today()->endOfWeek()->toDateString();
                break;
            case 'all':
                $this->date_start = '';
                $this->date_end = '';
                break;
        }
    }

    public function render()
    {
        $query = Shipping::with(['workOrder' => function($q) {
                $q->select('id', 'customer_address', 'shoe_brand', 'shoe_color');
            }])
            ->where('is_verified', 1);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('resi_pengiriman', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category) {
            $query->where('kategori_pengiriman', $this->category);
        }

        if ($this->date_start) {
            $query->whereDate('tanggal_pengiriman', '>=', $this->date_start);
        }

        if ($this->date_end) {
            $query->whereDate('tanggal_pengiriman', '<=', $this->date_end);
        }

        $shippings = $query->orderBy('tanggal_pengiriman', 'desc')
            ->paginate(24); // More items per page for grouping

        // Group by Date
        $groupedShippings = $shippings->getCollection()->groupBy(function($item) {
            return $item->tanggal_pengiriman?->format('Y-m-d') ?: 'Unknown';
        });

        // Stats
        $stats = [
            'today' => Shipping::where('is_verified', 1)->whereDate('tanggal_pengiriman', Carbon::today())->count(),
            'this_week' => Shipping::where('is_verified', 1)->whereBetween('tanggal_pengiriman', [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()])->count(),
        ];

        return view('livewire.cx.shipping-monitoring', [
            'shippings' => $shippings,
            'groupedShippings' => $groupedShippings,
            'stats' => $stats
        ])->layout('layouts.app', ['header' => 'Monitoring Pengiriman']);
    }
}
