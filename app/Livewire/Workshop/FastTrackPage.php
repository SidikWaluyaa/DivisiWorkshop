<?php

namespace App\Livewire\Workshop;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;

class FastTrackPage extends Component
{
    use WithPagination;

    #[Url(as: 'metric')]
    public string $selectedMetric = 'total_fast_track';

    #[Url(as: 'start_date')]
    public string $startDate = '';

    #[Url(as: 'end_date')]
    public string $endDate = '';

    #[Url(as: 'status')]
    public string $selectedStatus = '';

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'per_page')]
    public int $perPage = 15;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        if (empty($this->startDate)) {
            $this->startDate = now()->startOfMonth()->format('Y-m-d');
        }
        if (empty($this->endDate)) {
            $this->endDate = now()->format('Y-m-d');
        }
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSelectedStatus() { $this->resetPage(); }
    public function updatingSelectedMetric() { $this->resetPage(); }
    public function updatingStartDate() { $this->resetPage(); }
    public function updatingEndDate() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function setMetric(string $metric)
    {
        $this->selectedMetric = $metric;
        $this->resetPage();
    }

    #[Computed]
    public function stats()
    {
        // Query minimal untuk mendapatkan statistik angka aggregat
        $allOrders = WorkOrder::query()
            ->select('id', 'status', 'fast_track_status', 'created_at', 'entry_date')
            ->with(['logs' => function($q) {
                $q->whereIn('action', ['STATUS_CHANGE', 'fast_track_downgrade']);
            }])
            ->where(function($q) {
                $q->where('fast_track_status', 'yes')
                  ->orWhereHas('logs', function($l) {
                      $l->where('action', 'fast_track_downgrade');
                  });
            })
            ->where(function($q) {
                $q->whereBetween('entry_date', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ])
                ->orWhere(function($sub) {
                    $sub->where('status', 'SPK_PENDING')
                        ->whereBetween('created_at', [
                            Carbon::parse($this->startDate)->startOfDay(),
                            Carbon::parse($this->endDate)->endOfDay()
                        ]);
                });
            })
            ->get();

        // 1. Pending CS (Status SPK_PENDING)
        $pendingOrders = $allOrders->filter(function($o) {
            return $o->status->value === 'SPK_PENDING' && $o->fast_track_status === 'yes';
        });
        $pendingCount = $pendingOrders->count();

        // 2. SPK Workshop Aktif (Abaikan status SPK_PENDING)
        $orders = $allOrders->filter(function($o) {
            return $o->status->value !== 'SPK_PENDING';
        });

        $ftActiveOrders = $orders->where('fast_track_status', 'yes');
        $failedOrders = $ftActiveOrders->filter(function($order) {
            return $order->hasEverViolatedSla();
        });
        $operationalFailedOrders = $orders->filter(function($order) {
            $reason = $order->getNonSlaFailureReason();
            return $reason !== null && $reason !== 'TAMBAH_JASA';
        });
        $downgradedOrders = $allOrders->where('fast_track_status', 'no');

        // Extract available statuses for the current selected metric
        $metricOrders = collect();
        if ($this->selectedMetric === 'total_fast_track') {
            $metricOrders = $ftActiveOrders;
        } elseif ($this->selectedMetric === 'failed_fast_track') {
            $metricOrders = $failedOrders;
        } elseif ($this->selectedMetric === 'operational_failed_fast_track') {
            $metricOrders = $operationalFailedOrders;
        } elseif ($this->selectedMetric === 'pending_fast_track') {
            $metricOrders = $pendingOrders;
        } elseif ($this->selectedMetric === 'downgraded_fast_track') {
            $metricOrders = $downgradedOrders;
        }

        $availableStatuses = $metricOrders->pluck('status.value')->unique()->filter()->values()->toArray();

        return [
            'totalCount' => $ftActiveOrders->count(),
            'failedCount' => $failedOrders->count(),
            'operationalFailedCount' => $operationalFailedOrders->count(),
            'pendingCount' => $pendingCount,
            'downgradedCount' => $downgradedOrders->count(),
            'availableStatuses' => $availableStatuses,
            'allOrdersInMetric' => $metricOrders,
        ];
    }

    public function render()
    {
        $dateCol = ($this->selectedMetric === 'pending_fast_track') ? 'created_at' : 'entry_date';
        $stats = $this->stats;
        $filteredIds = $stats['allOrdersInMetric']->pluck('id')->toArray();

        $query = WorkOrder::query()
            ->with(['customer', 'logs', 'cxIssues'])
            ->whereIn('id', $filteredIds);

        // Apply status filter if set
        if (!empty($this->selectedStatus)) {
            $query->where('status', $this->selectedStatus);
        }

        // Apply search filter if set
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('shoe_brand', 'like', '%' . $this->search . '%')
                  ->orWhere('shoe_type', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function($c) {
                      $c->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Order by date descending
        $query->orderBy($dateCol, 'desc');

        // Calculate total filtered revenue before pagination
        $totalFilteredRevenue = $query->sum('total_transaksi');

        $orders = $query->paginate($this->perPage);

        return view('livewire.workshop.fast-track-page', [
            'orders' => $orders,
            'availableStatuses' => $stats['availableStatuses'],
            'totalFastTrack' => $stats['totalCount'],
            'failedFastTrack' => $stats['failedCount'],
            'operationalFailed' => $stats['operationalFailedCount'],
            'pendingFastTrack' => $stats['pendingCount'],
            'downgradedFastTrack' => $stats['downgradedCount'],
            'totalFilteredRevenue' => $totalFilteredRevenue,
        ]);
    }
}
