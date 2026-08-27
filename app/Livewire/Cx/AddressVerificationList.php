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
    public $shipping_status = 'all'; // 'all', 'shipped', 'preparing', 'workshop'

    // Modal State properties
    public $showSpkModal = false;
    public $selectedCustomerName = '';
    public $selectedCustomerSpks = [];

    public function openSpkModal($customerId)
    {
        $customer = Customer::with(['workOrders.shipping' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])->find($customerId);

        if ($customer) {
            $this->selectedCustomerName = $customer->name;
            $this->selectedCustomerSpks = $customer->workOrders->map(function($order) {
                $shipping = $order->shipping;
                $statusVal = is_object($order->status) ? $order->status->value : $order->status;
                
                // 1. Shipped: Verified or Has Resi or Status DIANTAR
                $isShipped = ($shipping && (bool)$shipping->is_verified) || !empty($shipping?->resi_pengiriman) || $statusVal === 'DIANTAR';

                // 2. Preparing: Already in shippings table but not yet shipped
                $isPreparingShipping = !$isShipped && !is_null($shipping);

                return [
                    'id' => $order->id,
                    'spk_number' => $order->spk_number,
                    'shoe_brand' => $order->shoe_brand,
                    'shoe_color' => $order->shoe_color,
                    'status' => is_object($order->status) ? $order->status->label() : $order->status,
                    'is_shipped' => $isShipped,
                    'is_preparing_shipping' => $isPreparingShipping,
                    'shipping_is_verified' => (bool) ($shipping?->is_verified ?? false),
                    'resi_pengiriman' => $shipping?->resi_pengiriman ?? null,
                    'ekspedisi' => $shipping?->ekspedisi ?? null,
                    'target_kirim' => $shipping?->target_kirim ? Carbon::parse($shipping->target_kirim)->format('d/m/Y') : null,
                    'tanggal_pengiriman' => $shipping?->tanggal_pengiriman ? Carbon::parse($shipping->tanggal_pengiriman)->format('d M Y') : null,
                    'kategori_pengiriman' => $shipping?->kategori_pengiriman ?? null,
                ];
            })->toArray();
            $this->showSpkModal = true;
        }
    }

    public function closeSpkModal()
    {
        $this->showSpkModal = false;
        $this->selectedCustomerName = '';
        $this->selectedCustomerSpks = [];
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'date_start' => ['except' => ''],
        'date_end' => ['except' => ''],
        'shipping_status' => ['except' => 'all'],
    ];

    public function mount()
    {
        $this->date_start = '';
        $this->date_end = '';
        $this->shipping_status = 'all';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingShippingStatus()
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

    public function resetFilters()
    {
        $this->search = '';
        $this->date_start = '';
        $this->date_end = '';
        $this->shipping_status = 'all';
        $this->resetPage();
    }

    public function render()
    {
        $query = Customer::query()
            ->with(['workOrders.shipping'])
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

        // Default to showing only today's verified addresses if no filters are active
        if (!$this->search && !$this->date_start && !$this->date_end && $this->shipping_status === 'all') {
            $query->whereDate('address_verified_at', Carbon::today());
        }

        // Filter by Detailed Shipping Status
        if ($this->shipping_status === 'shipped') {
            $query->whereHas('workOrders', function($woQ) {
                $woQ->whereHas('shipping', function($shipQ) {
                    $shipQ->where('is_verified', 1)->orWhereNotNull('resi_pengiriman');
                })->orWhere('status', 'DIANTAR');
            });
        } elseif ($this->shipping_status === 'preparing') {
            $query->whereHas('workOrders', function($woQ) {
                $woQ->whereHas('shipping', function($shipQ) {
                    $shipQ->where('is_verified', 0)->where(function($sq) {
                        $sq->whereNull('resi_pengiriman')->orWhere('resi_pengiriman', '');
                    });
                })->where('status', '!=', 'DIANTAR');
            });
        } elseif ($this->shipping_status === 'workshop') {
            $query->whereHas('workOrders', function($woQ) {
                $woQ->whereDoesntHave('shipping')->where('status', '!=', 'DIANTAR');
            });
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
