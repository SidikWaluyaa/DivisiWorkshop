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
            ->paginate(12);

        // Stats
        $stats = [
            'today' => Shipping::where('is_verified', 1)->whereDate('tanggal_pengiriman', Carbon::today())->count(),
            'this_week' => Shipping::where('is_verified', 1)->whereBetween('tanggal_pengiriman', [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()])->count(),
            'this_month' => Shipping::where('is_verified', 1)->whereMonth('tanggal_pengiriman', Carbon::today()->month)->count(),
        ];

        return view('livewire.cx.shipping-monitoring', [
            'shippings' => $shippings,
            'stats' => $stats
        ])->layout('layouts.app', ['header' => 'Monitoring Pengiriman']);
    }
}
