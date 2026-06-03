<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CxOverdueApiController extends Controller
{
    // SLA Definitions (in Days)
    public const STAGE_SLAS = [
        'PREPARATION' => 1,
        'SORTIR' => 1,
        'PRODUCTION' => 3,
        'QC' => 1,
        'REVISI' => 2,
        'SELESAI' => 2,
        'DIANTAR' => 1,
    ];

    public function index(Request $request)
    {
        $today = Carbon::now();

        // 1. Calculate Scoreboard Statistics
        $scoreboard = $this->calculateScoreboard($today);

        // 2. Build Filtered Query for Overdue Table
        $query = WorkOrder::query()
            ->leftJoin('shippings', 'shippings.work_order_id', '=', 'work_orders.id')
            ->select('work_orders.*')
            ->whereNotIn('work_orders.status', [
                WorkOrderStatus::BATAL->value, 
                WorkOrderStatus::DONASI->value,
                WorkOrderStatus::SPK_PENDING->value
            ])
            ->with('shipping');

        // Filter: Active card / Stage filter
        if ($request->filled('stage')) {
            $stage = strtoupper($request->stage);
            if ($stage === 'GLOBAL') {
                $query->where(function($q) {
                        $q->whereIn('work_orders.status', ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC', 'REVISI'])
                          ->orWhere(function($sub) {
                              $sub->where('work_orders.status', 'SELESAI')
                                  ->whereNull('work_orders.taken_date')
                                  ->where(function($inner) {
                                      $inner->whereNull('shippings.id')
                                            ->orWhere('shippings.is_verified', 1);
                                  });
                          })
                          ->orWhere(function($sub) {
                              $sub->where('work_orders.status', 'DIANTAR')
                                  ->orWhere(function($inner) {
                                      $inner->where('work_orders.status', 'SELESAI')
                                            ->whereNotNull('work_orders.taken_date')
                                            ->where('shippings.is_verified', 0);
                                  });
                          });
                    })
                    ->where(function($q) use ($today) {
                        $q->whereNull('work_orders.estimation_date')
                          ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                          ->orWhere('work_orders.estimation_date', '<', $today);
                    });
            } elseif ($stage === 'SELESAI') {
                $query->where('work_orders.status', 'SELESAI')
                    ->whereNull('work_orders.taken_date')
                    ->where(function($q) {
                        $q->whereNull('shippings.id')
                          ->orWhere('shippings.is_verified', 1);
                    })
                    ->where(function($q) use ($today) {
                        $q->whereNull('work_orders.estimation_date')
                          ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                          ->orWhere('work_orders.estimation_date', '<', $today);
                    });
            } elseif ($stage === 'DIANTAR') {
                $query->whereNotNull('shippings.id')
                    ->where('shippings.is_verified', 0)
                    ->where(function($q) use ($today) {
                        $q->whereNull('work_orders.estimation_date')
                          ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                          ->orWhere('work_orders.estimation_date', '<', $today);
                    });
            } else {
                $query->where('work_orders.status', $stage)
                    ->where(function($q) use ($today) {
                        $q->whereNull('work_orders.estimation_date')
                          ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                          ->orWhere('work_orders.estimation_date', '<', $today);
                    });
            }
        } else {
            // Default view: Show only overdue/alert items across all valid stages
            $query->where(function($q) use ($today) {
                // 1. Any REVISI status
                $q->where('work_orders.status', 'REVISI')
                  // 2. Or any other active status with passed/missing estimation_date
                  ->orWhere(function($sub) use ($today) {
                      $sub->whereIn('work_orders.status', ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'])
                          ->where(function($inner) use ($today) {
                              $inner->whereNull('work_orders.estimation_date')
                                    ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                                    ->orWhere('work_orders.estimation_date', '<', $today);
                          });
                  })
                  // 3. Or SELESAI status (without active/unverified shipping)
                  ->orWhere(function($sub) use ($today) {
                      $sub->where('work_orders.status', 'SELESAI')
                          ->whereNull('work_orders.taken_date')
                          ->where(function($inner) {
                              $inner->whereNull('shippings.id')
                                    ->orWhere('shippings.is_verified', 1);
                          })
                          ->where(function($inner) use ($today) {
                              $inner->whereNull('work_orders.estimation_date')
                                    ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                                    ->orWhere('work_orders.estimation_date', '<', $today);
                          });
                  })
                  // 4. Or active shipping (DIANTAR)
                  ->orWhere(function($sub) use ($today) {
                      $sub->whereNotNull('shippings.id')
                          ->where('shippings.is_verified', 0)
                          ->where(function($inner) use ($today) {
                              $inner->whereNull('work_orders.estimation_date')
                                    ->orWhere('work_orders.estimation_date', '<=', '2000-01-01')
                                    ->orWhere('work_orders.estimation_date', '<', $today);
                          });
                  });
            });
        }

        // Filter: Estimation Status
        if ($request->filled('filter_estimation')) {
            $est = $request->filter_estimation;
            if ($est === 'missing') {
                $query->where(function($q) {
                    $q->whereNull('work_orders.estimation_date')
                      ->orWhere('work_orders.estimation_date', '<=', '2000-01-01');
                });
            } elseif ($est === 'set') {
                $query->whereNotNull('work_orders.estimation_date')
                      ->where('work_orders.estimation_date', '>', '2000-01-01');
            }
        }

        // Filter: Search Box (SPK Number or Customer Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('work_orders.spk_number', 'like', "%{$search}%")
                  ->orWhere('work_orders.customer_name', 'like', "%{$search}%");
            });
        }

        // Filter: Customer Name specifically
        if ($request->filled('customer_name')) {
            $query->where('work_orders.customer_name', 'like', "%" . $request->customer_name . "%");
        }

        // Filter: Date Range (based on waktu stage entry date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('work_orders.waktu', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        // Get Paginated Data
        $perPage = $request->input('per_page', 25);
        $paginatedOrders = $query->paginate($perPage);

        // Map and Format Output Rows
        $formattedOrders = collect($paginatedOrders->items())->map(function($wo) use ($today) {
            return [
                'id' => $wo->id,
                'spk_number' => $wo->spk_number,
                'customer_name' => $wo->customer_name,
                'shoe_brand' => $wo->shoe_brand,
                'shoe_type' => $wo->shoe_type ?: '-',
                'current_stage' => $wo->status->label(),
                'stage_value' => $wo->status->value,
                'tgl_masuk_stage' => $wo->waktu ? Carbon::parse($wo->waktu)->toIso8601String() : null,
                'estimation_date' => $wo->estimation_date ? $wo->estimation_date->toIso8601String() : null,
                'days_overdue' => $this->calculateOverdueDays($wo, $today),
                'late_description' => $wo->late_description ?: 'Tidak ada catatan hambatan.',
                'detail_url' => route('admin.orders.show', $wo->id)
            ];
        });

        // Optional Sorting on collection (default: days_overdue desc)
        $sortBy = $request->input('sort_by', 'days_overdue');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy === 'days_overdue') {
            $formattedOrders = $sortOrder === 'desc' 
                ? $formattedOrders->sortByDesc('days_overdue')->values() 
                : $formattedOrders->sortBy('days_overdue')->values();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data overdue SLA berhasil ditarik.',
            'timestamp' => now()->toIso8601String(),
            'scoreboard' => $scoreboard,
            'orders' => $formattedOrders,
            'pagination' => [
                'total' => $paginatedOrders->total(),
                'per_page' => $paginatedOrders->perPage(),
                'current_page' => $paginatedOrders->currentPage(),
                'last_page' => $paginatedOrders->lastPage(),
                'from' => $paginatedOrders->firstItem(),
                'to' => $paginatedOrders->lastItem(),
            ]
        ]);
    }

    private function calculateOverdueDays(WorkOrder $wo, Carbon $today): int
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
        // A. Khusus DIANTAR (status DIANTAR atau memiliki shipping unverified)
        $isDiantar = ($stage === WorkOrderStatus::DIANTAR->value || ($wo->shipping && !$wo->shipping->is_verified));
        if ($isDiantar) {
            $entryDate = $wo->shipping ? $wo->shipping->tanggal_masuk : $entryDate;
            $sla = 1; // SLA for DIANTAR is 1 day
            return (int) max(0, abs($today->diffInDays(Carbon::parse($entryDate))) - $sla);
        }

        // B. Khusus SELESAI
        if ($stage === WorkOrderStatus::SELESAI->value) {
            $sla = 2; // SLA for SELESAI is 2 days
            return (int) max(0, abs($today->diffInDays(Carbon::parse($entryDate))) - $sla);
        }

        // C. Untuk status pengerjaan lainnya (PREPARATION, SORTIR, PRODUCTION, QC, REVISI) → selisih dari masuk stage
        return (int) abs($today->diffInDays(Carbon::parse($entryDate)));
    }

    private function calculateScoreboard(Carbon $today): array
    {
        $now = $today->toDateTimeString();

        // 1. Perhitungan days_overdue SQL
        $globalDaysOverdueSql = "
            CASE 
                WHEN work_orders.estimation_date IS NOT NULL AND work_orders.estimation_date > '2000-01-01' THEN
                    CASE WHEN work_orders.estimation_date < '{$now}' THEN DATEDIFF('{$now}', work_orders.estimation_date) ELSE 0 END
                WHEN shippings.id IS NOT NULL AND shippings.is_verified = 0 THEN
                    GREATEST(0, DATEDIFF('{$now}', COALESCE(shippings.tanggal_masuk, work_orders.waktu, work_orders.updated_at)) - 1)
                WHEN work_orders.status = 'SELESAI' THEN
                    GREATEST(0, DATEDIFF('{$now}', COALESCE(work_orders.waktu, work_orders.updated_at)) - 2)
                ELSE DATEDIFF('{$now}', COALESCE(work_orders.waktu, work_orders.updated_at))
            END
        ";
        
        $prepDaysOverdueSql = "
            CASE 
                WHEN work_orders.estimation_date IS NOT NULL AND work_orders.estimation_date > '2000-01-01' THEN
                    CASE WHEN work_orders.estimation_date < '{$now}' THEN DATEDIFF('{$now}', work_orders.estimation_date) ELSE 0 END
                ELSE DATEDIFF('{$now}', COALESCE(work_orders.waktu, work_orders.updated_at))
            END
        ";

        $results = WorkOrder::leftJoin('shippings', 'shippings.work_order_id', '=', 'work_orders.id')
            ->selectRaw("
            -- GLOBAL
            SUM(CASE WHEN (work_orders.status IN ('PREPARATION', 'SORTIR', 'PRODUCTION', 'QC', 'REVISI') OR (work_orders.status = 'SELESAI' AND work_orders.taken_date IS NULL AND (shippings.id IS NULL OR shippings.is_verified = 1)) OR (shippings.id IS NOT NULL AND shippings.is_verified = 0)) AND (work_orders.status = 'REVISI' OR (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}')) THEN 1 ELSE 0 END) as global_count,
            SUM(CASE WHEN (work_orders.status IN ('PREPARATION', 'SORTIR', 'PRODUCTION', 'QC', 'REVISI') OR (work_orders.status = 'SELESAI' AND work_orders.taken_date IS NULL AND (shippings.id IS NULL OR shippings.is_verified = 1)) OR (shippings.id IS NOT NULL AND shippings.is_verified = 0)) AND (work_orders.status = 'REVISI' OR (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}')) THEN {$globalDaysOverdueSql} ELSE 0 END) as global_sum,
            
            -- PREPARATION
            SUM(CASE WHEN work_orders.status = 'PREPARATION' AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN 1 ELSE 0 END) as preparation_count,
            SUM(CASE WHEN work_orders.status = 'PREPARATION' AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN {$prepDaysOverdueSql} ELSE 0 END) as preparation_sum,
            
            -- SORTIR
            SUM(CASE WHEN work_orders.status = 'SORTIR' AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN 1 ELSE 0 END) as sortir_count,
            SUM(CASE WHEN work_orders.status = 'SORTIR' AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN {$prepDaysOverdueSql} ELSE 0 END) as sortir_sum,
            
            -- PRODUCTION
            SUM(CASE WHEN work_orders.status = 'PRODUCTION' AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN 1 ELSE 0 END) as production_count,
            SUM(CASE WHEN work_orders.status = 'PRODUCTION' AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN {$prepDaysOverdueSql} ELSE 0 END) as production_sum,
            
            -- QC
            SUM(CASE WHEN work_orders.status = 'QC' AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN 1 ELSE 0 END) as qc_count,
            SUM(CASE WHEN work_orders.status = 'QC' AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN {$prepDaysOverdueSql} ELSE 0 END) as qc_sum,
            
            -- REVISI
            SUM(CASE WHEN work_orders.status = 'REVISI' THEN 1 ELSE 0 END) as revisi_count,
            SUM(CASE WHEN work_orders.status = 'REVISI' THEN {$prepDaysOverdueSql} ELSE 0 END) as revisi_sum,
            
            -- SELESAI
            SUM(CASE WHEN work_orders.status = 'SELESAI' AND work_orders.taken_date IS NULL AND (shippings.id IS NULL OR shippings.is_verified = 1) AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN 1 ELSE 0 END) as selesai_count,
            SUM(CASE WHEN work_orders.status = 'SELESAI' AND work_orders.taken_date IS NULL AND (shippings.id IS NULL OR shippings.is_verified = 1) AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN 
                (CASE 
                    WHEN work_orders.estimation_date IS NOT NULL AND work_orders.estimation_date > '2000-01-01' THEN
                        CASE WHEN work_orders.estimation_date < '{$now}' THEN DATEDIFF('{$now}', work_orders.estimation_date) ELSE 0 END
                    ELSE GREATEST(0, DATEDIFF('{$now}', COALESCE(work_orders.waktu, work_orders.updated_at)) - 2)
                END)
            ELSE 0 END) as selesai_sum,
            
            -- DIANTAR
            SUM(CASE WHEN shippings.id IS NOT NULL AND shippings.is_verified = 0 AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN 1 ELSE 0 END) as diantar_count,
            SUM(CASE WHEN shippings.id IS NOT NULL AND shippings.is_verified = 0 AND (work_orders.estimation_date IS NULL OR work_orders.estimation_date <= '2000-01-01' OR work_orders.estimation_date < '{$now}') THEN 
                (CASE 
                    WHEN work_orders.estimation_date IS NOT NULL AND work_orders.estimation_date > '2000-01-01' THEN
                        CASE WHEN work_orders.estimation_date < '{$now}' THEN DATEDIFF('{$now}', work_orders.estimation_date) ELSE 0 END
                    ELSE GREATEST(0, DATEDIFF('{$now}', COALESCE(shippings.tanggal_masuk, work_orders.waktu, work_orders.updated_at)) - 1)
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
}
