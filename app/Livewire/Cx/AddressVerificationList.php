<?php

namespace App\Livewire\Cx;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class AddressVerificationList extends Component
{
    use WithPagination;

    public $search = '';
    public $date_start = '';
    public $date_end = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'date_start' => ['except' => ''],
        'date_end' => ['except' => ''],
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
        $this->resetPage();
    }

    public function render()
    {
        $query = Customer::query()
            ->with(['workOrders' => function($q) {
                $q->whereNotIn('status', ['BATAL', 'SELESAI', 'DIANTAR', 'HISTORY', 'DONASI'])
                  ->orderBy('created_at', 'desc');
            }])
            ->where('is_address_verified', 1);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->date_start) {
            $query->whereDate('address_verified_at', '>=', $this->date_start);
        }

        if ($this->date_end) {
            $query->whereDate('address_verified_at', '<=', $this->date_end);
        }

        $customers = $query->orderBy('address_verified_at', 'desc')
            ->paginate(15);

        // Group by Date
        $groupedCustomers = $customers->getCollection()->groupBy(function($item) {
            return $item->address_verified_at?->format('Y-m-d') ?: 'Unknown';
        });

        // Stats
        $stats = [
            'today' => Customer::where('is_address_verified', 1)->whereDate('address_verified_at', Carbon::today())->count(),
            'this_week' => Customer::where('is_address_verified', 1)->whereBetween('address_verified_at', [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()])->count(),
            'total' => Customer::where('is_address_verified', 1)->count(),
        ];

        return view('livewire.cx.address-verification-list', [
            'customers' => $customers,
            'groupedCustomers' => $groupedCustomers,
            'stats' => $stats
        ])->layout('layouts.app', ['header' => 'Alamat Terverifikasi']);
    }
}
