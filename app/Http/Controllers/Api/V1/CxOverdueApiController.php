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
            ->whereNotIn('status', [
                WorkOrderStatus::BATAL->value, 
                WorkOrderStatus::DONASI->value,
                WorkOrderStatus::SPK_PENDING->value
            ]);

        // Filter: Active card / Stage filter
        if ($request->filled('stage')) {
            $stage = strtoupper($request->stage);
            if ($stage === 'GLOBAL') {
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
            } elseif ($stage === 'SELESAI') {
                $query->where('status', 'SELESAI')->whereNull('taken_date');
            } elseif ($stage === 'DIANTAR') {
                $query->where(function($q) {
                    $q->where('status', 'DIANTAR')
                      ->orWhere(function($sub) {
                          $sub->where('status', 'SELESAI')
                              ->whereNotNull('taken_date');
                      });
                });
            } else {
                $query->where('status', $stage);
            }
        }

        // Filter: Estimation Status
        if ($request->filled('filter_estimation')) {
            $est = $request->filter_estimation;
            if ($est === 'missing') {
                $query->where(function($q) {
                    $q->whereNull('estimation_date')
                      ->orWhere('estimation_date', '<=', '2000-01-01');
                });
            } elseif ($est === 'set') {
                $query->whereNotNull('estimation_date')
                      ->where('estimation_date', '>', '2000-01-01');
            }
        }

        // Filter: Search Box (SPK Number or Customer Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Filter: Customer Name specifically
        if ($request->filled('customer_name')) {
            $query->where('customer_name', 'like', "%" . $request->customer_name . "%");
        }

        // Filter: Date Range (based on waktu stage entry date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('waktu', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
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
        // A. Khusus SELESAI / DIANTAR → kurangi dengan SLA Stage masing-masing
        if ($stage === WorkOrderStatus::SELESAI->value || $stage === WorkOrderStatus::DIANTAR->value) {
            $sla = self::STAGE_SLAS[$stage] ?? 0;
            return (int) max(0, abs($today->diffInDays(Carbon::parse($entryDate))) - $sla);
        }

        // B. Untuk status pengerjaan lainnya (PREPARATION, SORTIR, PRODUCTION, QC, REVISI) → selisih dari masuk stage
        return (int) abs($today->diffInDays(Carbon::parse($entryDate)));
    }

    private function calculateScoreboard(Carbon $today): array
    {
        $stats = [];
        
        // 1. Global Overdue (PREPARATION, SORTIR, PRODUCTION, QC, REVISI, SELESAI, or DIANTAR with taken_date filled)
        $globalWo = WorkOrder::where(function($q) {
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
            })
            ->select('id', 'status', 'waktu', 'updated_at', 'estimation_date', 'taken_date')
            ->get();

        $globalLateDays = $globalWo->sum(function($wo) use ($today) {
            return $this->calculateOverdueDays($wo, $today);
        });

        $stats['GLOBAL'] = [
            'label' => 'Estimasi Kelewat (Global)',
            'overdue_count' => $globalWo->count(),
            'total_days_overdue' => (int) $globalLateDays,
            'color_theme' => 'amber',
            'sub_label' => 'Akumulasi Keterlambatan'
        ];

        // 2. Stage-Specific Overdue
        $stages = ['PREPARATION', 'SORTIR', 'PRODUCTION', 'QC', 'REVISI', 'SELESAI', 'DIANTAR'];
        foreach ($stages as $stage) {
            if ($stage === 'SELESAI') {
                $stageWo = WorkOrder::where('status', 'SELESAI')
                    ->whereNull('taken_date')
                    ->select('id', 'status', 'waktu', 'updated_at', 'estimation_date', 'taken_date')
                    ->get();
            } elseif ($stage === 'DIANTAR') {
                $stageWo = WorkOrder::where(function($q) {
                    $q->where('status', 'DIANTAR')
                      ->orWhere(function($sub) {
                          $sub->where('status', 'SELESAI')
                              ->whereNotNull('taken_date');
                      });
                })
                ->select('id', 'status', 'waktu', 'updated_at', 'estimation_date', 'taken_date')
                ->get();
            } else {
                $stageWo = WorkOrder::where('status', $stage)
                    ->select('id', 'status', 'waktu', 'updated_at', 'estimation_date', 'taken_date')
                    ->get();
            }
            $overdueCount = 0;
            $totalDaysOverdue = 0;

            foreach ($stageWo as $wo) {
                if ($stage === 'REVISI') {
                    $overdueCount++;
                    $totalDaysOverdue += $this->calculateOverdueDays($wo, $today);
                } else {
                    if (!$wo->estimation_date || $wo->estimation_date->year <= 2000 || $wo->estimation_date->lessThan($today)) {
                        $overdueCount++;
                        $totalDaysOverdue += $this->calculateOverdueDays($wo, $today);
                    }
                }
            }

            $theme = 'orange';
            if ($stage === 'REVISI') {
                $theme = 'rose';
            } elseif ($stage === 'SELESAI' || $stage === 'DIANTAR') {
                $theme = 'teal';
            }

            $stats[$stage] = [
                'label' => $stage === 'SELESAI' ? 'Selesai (Hold)' : ( $stage === 'DIANTAR' ? 'Diantar' : ucfirst(strtolower($stage)) ),
                'overdue_count' => $overdueCount,
                'total_days_overdue' => (int) $totalDaysOverdue,
                'color_theme' => $theme,
                'sub_label' => 'Akumulasi Keterlambatan'
            ];
        }

        return $stats;
    }
}
