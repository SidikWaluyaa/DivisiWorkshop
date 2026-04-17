<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\WorkOrderService;
use App\Models\WorkOrderLog;
use App\Models\Material;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class WorkshopMetricsService
{
    /**
     * Get real-time snapshot metrics
     */
    public function getSnapshotMetrics()
    {
        $productionStatuses = [
            WorkOrderStatus::PREPARATION,
            WorkOrderStatus::SORTIR,
            WorkOrderStatus::PRODUCTION,
            WorkOrderStatus::QC,
        ];

        $allActive = WorkOrder::whereIn('status', $productionStatuses)
            ->orWhere(function($q) use ($productionStatuses) {
                $q->where('status', WorkOrderStatus::CX_FOLLOWUP)
                  ->whereIn('previous_status', $productionStatuses);
            })
            ->get();

        return [
            'in_progress' => $allActive->count(),
            'urgent' => $allActive->filter(fn($o) => $o->days_remaining !== null && $o->days_remaining <= 3 && $o->days_remaining > 0)->count(),
            'overdue' => $allActive->filter(fn($o) => $o->is_overdue)->count(),
        ];
    }

    /**
     * Get historical metrics (Revenue, Lead Time, etc.)
     */
    public function getHistoricalMetrics(Carbon $start, Carbon $end)
    {
        $cacheKey = 'workshop_historical_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');

        return Cache::remember($cacheKey, 300, function() use ($start, $end) {
            $completed = WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereDate('finished_date', '>=', $start)
                ->whereDate('finished_date', '<=', $end->endOfDay())
                ->get();

            $throughput = $completed->count();
            $revenue = (float) $completed->sum('total_service_price');

            $avgLeadTime = 0;
            $qcPassRate = 0;

            if ($throughput > 0) {
                $totalDays = $completed->sum(fn($o) => $o->entry_date ? $o->entry_date->diffInDays($o->finished_date) : 0);
                $avgLeadTime = round($totalDays / $throughput, 1);
                $qcPassRate = round(($completed->where('is_revising', false)->count() / $throughput) * 100);
            }

            return [
                'throughput' => $throughput,
                'revenue' => $revenue,
                'avg_lead_time' => $avgLeadTime,
                'qc_pass_rate' => $qcPassRate,
            ];
        });
    }

    /**
     * Get Pipeline Distribution (Matches Dashboard Doughnut)
     */
    public function getPipelineStats(Carbon $start, Carbon $end)
    {
        $statuses = [
            WorkOrderStatus::ASSESSMENT->value => 'Assessment',
            WorkOrderStatus::PREPARATION->value => 'Preparation',
            WorkOrderStatus::SORTIR->value => 'Sortir',
            WorkOrderStatus::PRODUCTION->value => 'Production',
            WorkOrderStatus::QC->value => 'QC',
            WorkOrderStatus::SELESAI->value => 'Selesai',
            WorkOrderStatus::CX_FOLLOWUP->value => 'CX Follow Up',
        ];

        $counts = WorkOrder::where(function($q) use ($start, $end) {
                $q->whereBetween('entry_date', [$start, $end])
                  ->orWhereBetween('finished_date', [$start, $end]);
            })
            ->whereIn('status', array_keys($statuses))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $distribution = [];
        foreach ($statuses as $value => $label) {
            $distribution[strtolower(str_replace(' ', '_', $label))] = $counts[$value] ?? 0;
        }

        return array_merge($distribution, [
            'total_in_view' => array_sum($distribution)
        ]);
    }

    /**
     * Get Service Mix (Revenue by Service CATEGORY)
     */
    public function getServiceMix(Carbon $start, Carbon $end)
    {
        // Use a definitely unique cache key to force fresh grouping v4
        $cacheKey = 'workshop_service_mix_v4_category_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');

        return Cache::remember($cacheKey, 300, function() use ($start, $end) {
            return WorkOrderService::query()
                ->whereHas('workOrder', function($q) use ($start, $end) {
                    $q->where('status', WorkOrderStatus::SELESAI)
                      ->whereDate('finished_date', '>=', $start)
                      ->whereDate('finished_date', '<=', $end->endOfDay());
                })
                ->leftJoin('services', 'work_order_services.service_id', '=', 'services.id')
                ->select(
                    // Unique alias 'grp_category' ensures no conflict with table columns
                    DB::raw("COALESCE(
                        NULLIF(services.category, ''), 
                        NULLIF(work_order_services.category_name, ''), 
                        'Layanan Kustom'
                    ) as grp_category"),
                    DB::raw("COUNT(*) as total_count"),
                    DB::raw("SUM(work_order_services.cost) as total_revenue")
                )
                ->groupBy('grp_category')
                ->orderByDesc('total_revenue')
                ->get()
                ->map(fn($item) => [
                    'name' => $item->grp_category,
                    'count' => (int) $item->total_count,
                    'revenue' => (float) $item->total_revenue
                ]);
        });
    }

    /**
     * Get Service Leaderboard (Top Specific Services)
     */
    public function getServiceLeaderboard(Carbon $start, Carbon $end)
    {
        // New unique cache key for leaderboard v4
        $cacheKey = 'workshop_leaderboard_v4_specific_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');

        return Cache::remember($cacheKey, 300, function() use ($start, $end) {
            $results = WorkOrderService::query()
                ->whereHas('workOrder', function($q) use ($start, $end) {
                    $q->where('status', WorkOrderStatus::SELESAI)
                      ->whereDate('finished_date', '>=', $start)
                      ->whereDate('finished_date', '<=', $end->endOfDay());
                })
                ->leftJoin('services', 'work_order_services.service_id', '=', 'services.id')
                ->select(
                    // Unique alias 'grp_specific_name' ensures it uses our COALESCE logic
                    DB::raw("COALESCE(
                        NULLIF(services.name, ''), 
                        NULLIF(work_order_services.custom_service_name, ''), 
                        'Layanan Kustom'
                    ) as grp_specific_name"),
                    DB::raw("COUNT(*) as total_count"),
                    DB::raw("SUM(work_order_services.cost) as total_revenue")
                )
                ->groupBy('grp_specific_name')
                ->orderByDesc('total_revenue')
                ->take(10)
                ->get();

            return [
                'items' => $results->map(fn($item) => [
                    'name' => $item->grp_specific_name,
                    'count' => (int) $item->total_count,
                    'revenue' => (float) $item->total_revenue
                ])->toArray(),
                'total_revenue' => (float) $results->sum('total_revenue'),
                'total_count' => (int) $results->sum('total_count')
            ];
        });
    }

    /**
     * Get Trend Data (Inflow vs Completion + Performance Index)
     */
    public function getTrendData(Carbon $start, Carbon $end)
    {
        $cacheKey = 'workshop_trends_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');

        return Cache::remember($cacheKey, 300, function() use ($start, $end) {
            $completions = WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereDate('finished_date', '>=', $start)
                ->whereDate('finished_date', '<=', $end->endOfDay())
                ->selectRaw('DATE(finished_date) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');

            $entries = WorkOrder::whereDate('entry_date', '>=', $start)
                ->whereDate('entry_date', '<=', $end->endOfDay())
                ->selectRaw('DATE(entry_date) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');

            $labels = [];
            $completionData = [];
            $entryData = [];

            foreach (CarbonPeriod::create($start, $end) as $date) {
                $key = $date->format('Y-m-d');
                $labels[] = $date->format('d M');
                $completionData[] = $completions[$key] ?? 0;
                $entryData[] = $entries[$key] ?? 0;
            }

            $totalInflow = array_sum($entryData);
            $totalCompletion = array_sum($completionData);
            $performanceIndex = $totalInflow > 0 ? round(($totalCompletion / $totalInflow) * 100) : ($totalCompletion > 0 ? 100 : 0);

            return [
                'labels' => $labels,
                'completion' => $completionData,
                'inflow' => $entryData,
                'summary' => [
                    'total_inflow' => $totalInflow,
                    'total_completion' => $totalCompletion,
                    'performance_index' => $performanceIndex . '%'
                ]
            ];
        });
    }

    /**
     * Get Workload Distribution (Filtered by Date Range)
     */
    public function getWorkloadStats(Carbon $start, Carbon $end)
    {
        // 1. Station Counts (Orders created or modified in period)
        $stations = [
            'Assessment' => WorkOrder::where('status', WorkOrderStatus::ASSESSMENT)->whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)->count(),
            'Preparation' => WorkOrder::where('status', WorkOrderStatus::PREPARATION)->whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)->count(),
            'Sortir' => WorkOrder::where('status', WorkOrderStatus::SORTIR)->whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)->count(),
            'Production' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION)->where('is_revising', false)->whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)->count(),
            'QC' => WorkOrder::whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)->where(fn($q) => $q->where('status', WorkOrderStatus::QC)->orWhere(fn($sq) => $sq->where('status', WorkOrderStatus::PRODUCTION)->where('is_revising', true)))->count(),
        ];

        // 2. Identify Bottleneck
        $sorted = collect($stations)->sortDesc();
        $bottleneck = $sorted->keys()->first();
        $bottleneckCount = $sorted->first();

        // 3. Optimize Technician Load
        $technicians = User::where('role', '!=', 'customer')
            ->select('id', 'name')
            ->get()
            ->map(function($user) use ($start, $end) {
                return [
                    'name' => $user->name,
                    'count' => WorkOrder::whereDate('entry_date', '>=', $start)->whereDate('entry_date', '<=', $end)
                        ->whereIn('status', [WorkOrderStatus::PRODUCTION, WorkOrderStatus::QC])
                        ->where(function($q) use ($user) {
                            $q->where('technician_production_id', $user->id)
                              ->orWhere('qc_final_pic_id', $user->id)
                              ->orWhere('prod_sol_by', $user->id)
                              ->orWhere('prod_upper_by', $user->id)
                              ->orWhere('prod_cleaning_by', $user->id);
                        })->count()
                ];
            })
            ->where('count', '>', 0)
            ->sortByDesc('count')
            ->take(10)
            ->values();

        return [
            'stations' => $stations,
            'bottleneck' => [
                'station' => $bottleneck,
                'count' => $bottleneckCount
            ],
            'technicians' => $technicians
        ];
    }

    /**
     * Get Urgent Order List (Filtered by Date Range)
     */
    public function getUrgentOrderList(Carbon $start, Carbon $end)
    {
        $activeStatuses = [
            WorkOrderStatus::ASSESSMENT,
            WorkOrderStatus::PREPARATION,
            WorkOrderStatus::SORTIR,
            WorkOrderStatus::PRODUCTION,
            WorkOrderStatus::QC,
        ];

        return WorkOrder::whereIn('status', $activeStatuses)
            ->whereDate('entry_date', '>=', $start)
            ->whereDate('entry_date', '<=', $end)
            ->whereNotNull('estimation_date')
            ->orderByRaw("DATEDIFF(estimation_date, NOW()) ASC")
            ->take(20)
            ->get()
            ->map(fn($o) => [
                'spk_number' => $o->spk_number,
                'customer' => $o->customer_name,
                'status' => $o->status->value,
                'est_date' => $o->estimation_date->toDateString(),
                'days_left' => $o->days_remaining,
                'is_late' => $o->is_overdue
            ]);
    }

    /**
     * Get Material Alerts
     */
    public function getMaterialAlerts()
    {
        return Material::whereRaw('stock < min_stock')
            ->orderBy('stock', 'asc')
            ->take(10)
            ->get()
            ->map(fn($m) => [
                'name' => $m->name,
                'stock' => $m->stock,
                'min' => $m->min_stock,
                'unit' => $m->unit
            ]);
    }

    /**
     * Get Recent Activity Feed (Filtered by Date Range)
     */
    public function getRecentActivity(Carbon $start, Carbon $end)
    {
        return WorkOrderLog::whereBetween('created_at', [$start, $end->endOfDay()])
            ->latest()
            ->take(15)
            ->with(['user', 'workOrder'])
            ->get()
            ->map(fn($log) => [
                'user' => $log->user?->name ?? 'System',
                'spk' => $log->workOrder?->spk_number ?? 'N/A',
                'action' => $log->action,
                'details' => $log->details,
                'time' => $log->created_at->diffForHumans()
            ]);
    }
}
