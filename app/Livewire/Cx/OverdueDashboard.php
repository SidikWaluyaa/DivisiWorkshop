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
        $now = $today->toDateTimeString();
        $query = WorkOrder::query()
            ->select('*')
            ->selectRaw("
                CASE 
                    -- 1. Prioritas Utama: Jika estimasi tersedia dan valid
                    WHEN estimation_date IS NOT NULL AND estimation_date > '2000-01-01' THEN
                        CASE 
                            WHEN estimation_date < ? THEN DATEDIFF(?, estimation_date)
                            ELSE 0
                        END
                    -- 2. Fallback: Jika estimasi belum di-set
                    -- A. Khusus SELESAI / DIANTAR (SLA-based)
                    WHEN status IN ('SELESAI', 'DIANTAR') THEN
                        GREATEST(0, DATEDIFF(?, COALESCE(waktu, updated_at)) - 
                            CASE 
                                WHEN status = 'SELESAI' THEN 2
                                WHEN status = 'DIANTAR' THEN 1
                                ELSE 0
                            END
                        )
                    -- B. Untuk status pengerjaan lainnya (PREPARATION, SORTIR, PRODUCTION, QC, REVISI)
                    ELSE DATEDIFF(?, COALESCE(waktu, updated_at))
                END as days_overdue
            ", [$now, $now, $now, $now]);

        // Filter: Active card / Stage filter
        if ($this->activeCard) {
            if ($this->activeCard === 'GLOBAL') {
                $query->where(function($q) {
                        $q->whereIn('status', ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC', 'REVISI', 'SELESAI'])
                          ->orWhere(function($sub) {
                              $sub->where('status', 'DIANTAR')
                                  ->whereNotNull('taken_date');
                          });
                    })
                    ->where(function($q) use ($today) {
                        $q->whereNull('estimation_date')
                          ->orWhere('estimation_date', '<=', '2000-01-01')
                          ->orWhere('estimation_date', '<', $today);
                    });
            } elseif ($this->activeCard === 'SELESAI') {
                $query->where('status', 'SELESAI')
                    ->whereNull('taken_date')
                    ->where(function($q) use ($today) {
                        $q->whereNull('estimation_date')
                          ->orWhere('estimation_date', '<=', '2000-01-01')
                          ->orWhere('estimation_date', '<', $today);
                    });
            } elseif ($this->activeCard === 'DIANTAR') {
                $query->where(function($q) {
                    $q->where('status', 'DIANTAR')
                      ->orWhere(function($sub) {
                          $sub->where('status', 'SELESAI')
                              ->whereNotNull('taken_date');
                      });
                })
                ->where(function($q) use ($today) {
                    $q->whereNull('estimation_date')
                      ->orWhere('estimation_date', '<=', '2000-01-01')
                      ->orWhere('estimation_date', '<', $today);
                });
            } elseif ($this->activeCard === 'REVISI') {
                $query->where('status', 'REVISI');
            } else {
                // PREPARATION, SORTIR, PRODUCTION, QC
                $query->where('status', $this->activeCard)
                    ->where(function($q) use ($today) {
                        $q->whereNull('estimation_date')
                          ->orWhere('estimation_date', '<=', '2000-01-01')
                          ->orWhere('estimation_date', '<', $today);
                    });
            }
        } else {
            // Default view: Show only overdue/alert items across all valid stages
            $query->where(function($q) use ($today) {
                // 1. Any REVISI status
                $q->where('status', 'REVISI')
                  // 2. Or any other active status with passed/missing estimation_date
                  ->orWhere(function($sub) use ($today) {
                      $sub->whereIn('status', ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC', 'SELESAI', 'DIANTAR'])
                          ->where(function($inner) use ($today) {
                              $inner->whereNull('estimation_date')
                                    ->orWhere('estimation_date', '<=', '2000-01-01')
                                    ->orWhere('estimation_date', '<', $today);
                          });
                  });
            });
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

        // Filter: Date Range (based on waktu stage entry date)
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('waktu', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        }

        return $query;
    }

    private function calculateDaysOverdue($wo, Carbon $today): int
    {
        $stage = $wo->status->value;
        $entryDate = $wo->waktu ?: $wo->updated_at;

        // 1. Prioritas Utama: Jika estimasi tersedia
        if ($wo->estimation_date && $wo->estimation_date->year > 2000) {
            if ($wo->estimation_date->lessThan($today)) {
                return (int) abs($today->diffInDays($wo->estimation_date));
            }
            return 0; // On Track
        }

        // 2. Fallback: Jika estimasi belum di-set
        // A. Khusus SELESAI / DIANTAR → kurangi dengan SLA Stage masing-masing
        if ($stage === WorkOrderStatus::SELESAI->value || $stage === WorkOrderStatus::DIANTAR->value) {
            $sla = CxOverdueApiController::STAGE_SLAS[$stage] ?? 0;
            return (int) max(0, abs($today->diffInDays(Carbon::parse($entryDate))) - $sla);
        }

        // B. Untuk status pengerjaan lainnya (PREPARATION, SORTIR, PRODUCTION, QC, REVISI) → selisih dari masuk stage
        return (int) abs($today->diffInDays(Carbon::parse($entryDate)));
    }

    private function calculateScoreboard(Carbon $today): array
    {
        $now = $today->toDateTimeString();

        // 1. Perhitungan days_overdue SQL
        $globalDaysOverdueSql = "
            CASE 
                WHEN estimation_date IS NOT NULL AND estimation_date > '2000-01-01' THEN
                    CASE WHEN estimation_date < '{$now}' THEN DATEDIFF('{$now}', estimation_date) ELSE 0 END
                WHEN status IN ('SELESAI', 'DIANTAR') THEN
                    GREATEST(0, DATEDIFF('{$now}', COALESCE(waktu, updated_at)) - CASE WHEN status = 'SELESAI' THEN 2 WHEN status = 'DIANTAR' THEN 1 ELSE 0 END)
                ELSE DATEDIFF('{$now}', COALESCE(waktu, updated_at))
            END
        ";
        
        $prepDaysOverdueSql = "
            CASE 
                WHEN estimation_date IS NOT NULL AND estimation_date > '2000-01-01' THEN
                    CASE WHEN estimation_date < '{$now}' THEN DATEDIFF('{$now}', estimation_date) ELSE 0 END
                ELSE DATEDIFF('{$now}', COALESCE(waktu, updated_at))
            END
        ";

        $results = WorkOrder::selectRaw("
            -- GLOBAL
            SUM(CASE WHEN (status IN ('PREPARATION', 'SORTIR', 'PRODUCTION', 'QC', 'REVISI', 'SELESAI') OR (status = 'DIANTAR' AND taken_date IS NOT NULL)) AND (status = 'REVISI' OR (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}')) THEN 1 ELSE 0 END) as global_count,
            SUM(CASE WHEN (status IN ('PREPARATION', 'SORTIR', 'PRODUCTION', 'QC', 'REVISI', 'SELESAI') OR (status = 'DIANTAR' AND taken_date IS NOT NULL)) AND (status = 'REVISI' OR (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}')) THEN {$globalDaysOverdueSql} ELSE 0 END) as global_sum,
            
            -- PREPARATION
            SUM(CASE WHEN status = 'PREPARATION' AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN 1 ELSE 0 END) as preparation_count,
            SUM(CASE WHEN status = 'PREPARATION' AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN {$prepDaysOverdueSql} ELSE 0 END) as preparation_sum,
            
            -- SORTIR
            SUM(CASE WHEN status = 'SORTIR' AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN 1 ELSE 0 END) as sortir_count,
            SUM(CASE WHEN status = 'SORTIR' AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN {$prepDaysOverdueSql} ELSE 0 END) as sortir_sum,
            
            -- PRODUCTION
            SUM(CASE WHEN status = 'PRODUCTION' AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN 1 ELSE 0 END) as production_count,
            SUM(CASE WHEN status = 'PRODUCTION' AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN {$prepDaysOverdueSql} ELSE 0 END) as production_sum,
            
            -- QC
            SUM(CASE WHEN status = 'QC' AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN 1 ELSE 0 END) as qc_count,
            SUM(CASE WHEN status = 'QC' AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN {$prepDaysOverdueSql} ELSE 0 END) as qc_sum,
            
            -- REVISI
            SUM(CASE WHEN status = 'REVISI' THEN 1 ELSE 0 END) as revisi_count,
            SUM(CASE WHEN status = 'REVISI' THEN {$prepDaysOverdueSql} ELSE 0 END) as revisi_sum,
            
            -- SELESAI
            SUM(CASE WHEN status = 'SELESAI' AND taken_date IS NULL AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN 1 ELSE 0 END) as selesai_count,
            SUM(CASE WHEN status = 'SELESAI' AND taken_date IS NULL AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN 
                (CASE 
                    WHEN estimation_date IS NOT NULL AND estimation_date > '2000-01-01' THEN
                        CASE WHEN estimation_date < '{$now}' THEN DATEDIFF('{$now}', estimation_date) ELSE 0 END
                    ELSE GREATEST(0, DATEDIFF('{$now}', COALESCE(waktu, updated_at)) - 2)
                END)
            ELSE 0 END) as selesai_sum,
            
            -- DIANTAR
            SUM(CASE WHEN (status = 'DIANTAR' OR (status = 'SELESAI' AND taken_date IS NOT NULL)) AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN 1 ELSE 0 END) as diantar_count,
            SUM(CASE WHEN (status = 'DIANTAR' OR (status = 'SELESAI' AND taken_date IS NOT NULL)) AND (estimation_date IS NULL OR estimation_date <= '2000-01-01' OR estimation_date < '{$now}') THEN 
                (CASE 
                    WHEN estimation_date IS NOT NULL AND estimation_date > '2000-01-01' THEN
                        CASE WHEN estimation_date < '{$now}' THEN DATEDIFF('{$now}', estimation_date) ELSE 0 END
                    ELSE GREATEST(0, DATEDIFF('{$now}', COALESCE(waktu, updated_at)) - 1)
                END)
            ELSE 0 END) as diantar_sum
        ")->first();

        $stats = [];
        
        $stats['GLOBAL'] = [
            'label' => 'Estimasi Kelewat (Global)',
            'overdue_count' => (int) ($results->global_count ?? 0),
            'total_days_overdue' => (int) ($results->global_sum ?? 0),
            'color_theme' => 'amber',
            'sub_label' => 'Akumulasi Keterlambatan'
        ];

        $stages = ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC', 'REVISI', 'SELESAI', 'DIANTAR'];
        foreach ($stages as $stage) {
            $colPrefix = strtolower($stage);
            $countKey = $colPrefix . '_count';
            $sumKey = $colPrefix . '_sum';
            
            $stats[$stage] = [
                'label' => $stage === 'SELESAI' ? 'Selesai (Hold)' : ( $stage === 'DIANTAR' ? 'Diantar' : ucfirst(strtolower($stage)) ),
                'overdue_count' => (int) ($results->$countKey ?? 0),
                'total_days_overdue' => (int) ($results->$sumKey ?? 0),
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

        // 2. Fetch WorkOrders with real-time days_overdue calculation by MySQL
        $query = $this->buildQuery($today);

        // Apply database-level sorting and pagination (extremely fast)
        $paginated = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(25);

        return view('livewire.cx.overdue-dashboard', [
            'orders' => $paginated,
            'scoreboard' => $scoreboard,
            'apiKey' => config('app.dashboard_api_key')
        ])->layout('layouts.app');
    }
}
