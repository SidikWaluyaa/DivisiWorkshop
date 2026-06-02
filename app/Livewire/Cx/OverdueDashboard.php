<?php

namespace App\Livewire\Cx;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use App\Http\Controllers\Api\V1\CxOverdueApiController;
use Carbon\Carbon;

class OverdueDashboard extends Component
{
    use WithPagination;

    // Filters
    public $searchSpk = '';
    public $searchCustomer = '';
    public $startDate = '';
    public $endDate = '';
    public $filterEstimation = 'all'; // 'all', 'missing', 'set'
    
    // Clicking Card Filter
    public $activeCard = null; // 'GLOBAL', 'PREPARATION', 'SORTIR', etc.

    // Sorting
    public $sortBy = 'days_overdue';
    public $sortDirection = 'desc';

    protected $queryString = [
        'activeCard' => ['except' => null, 'as' => 'card'],
        'searchSpk' => ['except' => '', 'as' => 'spk'],
        'searchCustomer' => ['except' => '', 'as' => 'customer'],
        'startDate' => ['except' => '', 'as' => 'start'],
        'endDate' => ['except' => '', 'as' => 'end'],
        'filterEstimation' => ['except' => 'all', 'as' => 'est'],
    ];

    public function selectCard($card)
    {
        $this->activeCard = ($this->activeCard === $card) ? null : $card;
        $this->resetPage();
    }

    public function sortByField($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['searchSpk', 'searchCustomer', 'startDate', 'endDate', 'activeCard', 'filterEstimation']);
        $this->resetPage();
    }

    public function exportToCsv()
    {
        $today = Carbon::now();
        $query = $this->buildQuery($today);
        $orders = $query->get();

        $csvFileName = 'CX_Overdue_SLA_Export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No. SPK', 'Nama Pelanggan', 'Brand', 'Tipe Sepatu', 'Stage', 'Tgl Masuk Stage', 'Estimasi Selesai', 'Hari Kelewat', 'Keterangan'];

        $callback = function() use($orders, $columns, $today) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $wo) {
                $days = $this->calculateDaysOverdue($wo, $today);
                fputcsv($file, [
                    $wo->spk_number,
                    $wo->customer_name,
                    $wo->shoe_brand,
                    $wo->shoe_type ?: '-',
                    $wo->status->label(),
                    $wo->waktu ? $wo->waktu->format('Y-m-d H:i') : $wo->updated_at->format('Y-m-d H:i'),
                    $wo->estimation_date ? $wo->estimation_date->format('Y-m-d') : '-',
                    $days,
                    $wo->late_description ?: 'Tidak ada catatan.'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildQuery(Carbon $today)
    {
        $query = WorkOrder::query()
            ->whereNotIn('status', [
                WorkOrderStatus::BATAL->value, 
                WorkOrderStatus::DONASI->value,
                WorkOrderStatus::SPK_PENDING->value
            ]);

        // Filter: Active card / Stage filter
        if ($this->activeCard) {
            if ($this->activeCard === 'GLOBAL') {
                $query->whereNotIn('status', [
                        WorkOrderStatus::SELESAI->value,
                        WorkOrderStatus::DIANTAR->value,
                        WorkOrderStatus::SPK_PENDING->value,
                        WorkOrderStatus::BATAL->value,
                        WorkOrderStatus::DONASI->value,
                    ])
                    ->where(function($q) use ($today) {
                        $q->whereNull('estimation_date')
                          ->orWhere('estimation_date', '<=', '2000-01-01')
                          ->orWhere('estimation_date', '<', $today);
                    });
            } elseif ($this->activeCard === 'SELESAI') {
                $query->where('status', 'SELESAI')->whereNull('taken_date');
            } elseif ($this->activeCard === 'DIANTAR') {
                $query->where(function($q) {
                    $q->where('status', 'DIANTAR')
                      ->orWhere(function($sub) {
                          $sub->where('status', 'SELESAI')
                              ->whereNotNull('taken_date');
                      });
                });
            } else {
                $query->where('status', $this->activeCard);
            }
        }

        // Filter: Estimation Status
        if ($this->filterEstimation === 'missing') {
            $query->where(function($q) {
                $q->whereNull('estimation_date')
                  ->orWhere('estimation_date', '<=', '2000-01-01');
            });
        } elseif ($this->filterEstimation === 'set') {
            $query->whereNotNull('estimation_date')
                  ->where('estimation_date', '>', '2000-01-01');
        }

        // Filter: Search Box for SPK Number
        if ($this->searchSpk) {
            $query->where('spk_number', 'like', "%{$this->searchSpk}%");
        }

        // Filter: Customer Name
        if ($this->searchCustomer) {
            $query->where('customer_name', 'like', "%{$this->searchCustomer}%");
        }

        // Filter: Date Range (based on entry_date)
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('entry_date', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        }

        return $query;
    }

    private function calculateDaysOverdue($wo, Carbon $today): int
    {
        $stage = $wo->status->value;
        $entryDate = $wo->waktu ?: $wo->updated_at;
        
        if ($stage === WorkOrderStatus::SELESAI->value || $stage === WorkOrderStatus::DIANTAR->value) {
            $sla = CxOverdueApiController::STAGE_SLAS[$stage] ?? 0;
            return (int) max(0, abs($today->diffInDays(Carbon::parse($entryDate))) - $sla);
        } elseif (!$wo->estimation_date || $wo->estimation_date->lessThan(Carbon::parse('2000-01-01'))) {
            // Missing or invalid estimation date!
            return -1;
        } elseif ($wo->estimation_date && $wo->estimation_date->lessThan($today)) {
            return (int) abs($today->diffInDays(Carbon::parse($wo->estimation_date)));
        } elseif (isset(CxOverdueApiController::STAGE_SLAS[$stage])) {
            $sla = CxOverdueApiController::STAGE_SLAS[$stage];
            return (int) max(0, abs($today->diffInDays(Carbon::parse($entryDate))) - $sla);
        }

        return 0;
    }

    private function calculateScoreboard(Carbon $today): array
    {
        $stats = [];
        
        // 1. Global Overdue (All active stages except pending/completed/canceled, past estimation_date OR missing estimation_date)
        $globalWo = WorkOrder::whereNotIn('status', [
                WorkOrderStatus::SELESAI->value,
                WorkOrderStatus::DIANTAR->value,
                WorkOrderStatus::SPK_PENDING->value,
                WorkOrderStatus::BATAL->value,
                WorkOrderStatus::DONASI->value,
            ])
            ->where(function($q) use ($today) {
                $q->whereNull('estimation_date')
                  ->orWhere('estimation_date', '<=', '2000-01-01')
                  ->orWhere('estimation_date', '<', $today);
            })
            ->get();

        $stats['GLOBAL'] = [
            'label' => 'Estimasi Kelewat (Global)',
            'overdue_count' => $globalWo->count(),
            'total_days_overdue' => $globalWo->sum(function($wo) use ($today) {
                if (!$wo->estimation_date || $wo->estimation_date->lessThan(Carbon::parse('2000-01-01'))) {
                    return 0; // Skip invalid dates in sum contribution
                }
                return (int) abs($today->diffInDays($wo->estimation_date));
            }),
            'color_theme' => 'amber',
            'sub_label' => 'Akumulasi Keterlambatan'
        ];

        // 2. Stage-Specific Overdue
        foreach (CxOverdueApiController::STAGE_SLAS as $stage => $sla) {
            if ($stage === 'SELESAI') {
                $stageWo = WorkOrder::where('status', 'SELESAI')->whereNull('taken_date')->get();
            } elseif ($stage === 'DIANTAR') {
                $stageWo = WorkOrder::where(function($q) {
                    $q->where('status', 'DIANTAR')
                      ->orWhere(function($sub) {
                          $sub->where('status', 'SELESAI')
                              ->whereNotNull('taken_date');
                      });
                })->get();
            } else {
                $stageWo = WorkOrder::where('status', $stage)->get();
            }
            $overdueCount = 0;
            $totalDaysOverdue = 0;

            foreach ($stageWo as $wo) {
                $entryDate = $wo->waktu ?: $wo->updated_at;
                $days = (int) max(0, abs($today->diffInDays(Carbon::parse($entryDate))) - $sla);
                if ($days > 0) {
                    $overdueCount++;
                    $totalDaysOverdue += $days;
                }
            }

            $stats[$stage] = [
                'label' => $stage === 'SELESAI' ? 'Selesai (Hold)' : ( $stage === 'DIANTAR' ? 'Diantar' : ucfirst(strtolower($stage)) ),
                'overdue_count' => $overdueCount,
                'total_days_overdue' => $totalDaysOverdue,
                'color_theme' => ($stage === 'REVISI') ? 'rose' : (($stage === 'SELESAI' || $stage === 'DIANTAR') ? 'teal' : 'orange'),
                'sub_label' => 'Akumulasi Keterlambatan'
            ];
        }

        return $stats;
    }

    public function render()
    {
        $today = Carbon::now();

        // 1. Calculate stats for the 8 cards
        $scoreboard = $this->calculateScoreboard($today);

        // 2. Fetch WorkOrders
        $query = $this->buildQuery($today);

        // Retrieve and paginate raw items to handle sorting of virtual attribute (days_overdue)
        $allOrders = $query->get()->map(function($wo) use ($today) {
            $wo->days_overdue = $this->calculateDaysOverdue($wo, $today);
            return $wo;
        });

        // Apply sorting based on collection
        if ($this->sortDirection === 'desc') {
            $sorted = $allOrders->sortByDesc($this->sortBy);
        } else {
            $sorted = $allOrders->sortBy($this->sortBy);
        }

        // Handle manually-driven collection pagination
        $currentPage = $this->getPage();
        $perPage = 25;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $sorted->forPage($currentPage, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $currentPage,
            ['path' => route('cx.overdue-dashboard')]
        );

        return view('livewire.cx.overdue-dashboard', [
            'orders' => $paginated,
            'scoreboard' => $scoreboard,
            'apiKey' => config('app.dashboard_api_key')
        ])->layout('layouts.app');
    }
}
