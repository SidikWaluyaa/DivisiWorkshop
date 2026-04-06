<?php

namespace App\Services;

use App\Models\CxIssue;
use App\Models\WorkOrder;
use App\Models\WorkOrderService;
use App\Models\OTO;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CxDashboardService
{
    /**
     * Get CX Performance and Upsell Metrics for a date range
     */
    public function getSummary(Carbon $start, Carbon $end, bool $forceRefresh = false)
    {
        $cacheKey = "cx_dashboard_summary_v2_{$start->format('Ymd')}_{$end->format('Ymd')}";

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addMinutes(1), function () use ($start, $end) {
            return [
                'kpi' => $this->getKpiMetrics($start, $end),
                'upsell' => $this->getUpsellMetrics($start, $end),
                'trend' => $this->getTrendData($start, $end),
                'problems' => $this->getTopProblemData($start, $end),
                'source' => $this->getIssuesBySource($start, $end),
                'resolvers' => $this->getTopResolvers($start, $end),
                'recent' => $this->getRecentIssues($start, $end),
                'overdue' => $this->getOverdueIssues(),
                'period' => [
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString(),
                ],
                'last_updated' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * KPI Basic Metrics
     */
    private function getKpiMetrics(Carbon $start, Carbon $end)
    {
        $totalIssues = CxIssue::whereBetween('created_at', [$start, $end])->count();
        $openIssues = CxIssue::where('status', 'OPEN')->whereBetween('created_at', [$start, $end])->count();
        $inProgressIssues = CxIssue::where('status', 'IN_PROGRESS')->whereBetween('created_at', [$start, $end])->count();
        $resolvedIssues = CxIssue::where('status', 'RESOLVED')
            ->whereBetween('resolved_at', [$start, $end])
            ->whereHas('workOrder', function($q) {
                $q->where('status', '!=', 'BATAL');
            })->count();
        
        $cancelledIssues = CxIssue::where('status', 'RESOLVED')
            ->whereBetween('resolved_at', [$start, $end])
            ->whereHas('workOrder', function($q) {
                $q->where('status', 'BATAL');
            })->count();

        $avgResponseTime = CxIssue::where('status', 'RESOLVED')
            ->whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$start, $end])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        $resolutionRate = $totalIssues > 0 ? round(($resolvedIssues / $totalIssues) * 100, 1) : 0;

        // Resolved Breakdown logic (from original controller)
        $resolvedIssuesQuery = CxIssue::where('status', 'RESOLVED')
            ->whereBetween('resolved_at', [$start, $end])
            ->whereHas('workOrder', function($q) {
                $q->where('status', '!=', 'BATAL');
            });
            
        $resolvedWithUpsell = (clone $resolvedIssuesQuery)
            ->where(function($q) {
                $q->where('cx_issues.resolution_type', 'tambah_jasa')
                  ->orWhere(function($sq) {
                      // Safety Net: "Lanjut" but services were actually added
                      $sq->where('cx_issues.resolution_type', 'lanjut')
                         ->whereHas('workOrder.workOrderServices', function($ssq) {
                             $ssq->whereRaw('work_order_services.created_at >= cx_issues.created_at')
                                 ->where(function($sssq) {
                                     // Logic: notes MUST contain a significant word from category/custom name
                                     // DUAL-CHECK: We check resolution_notes AND technician_notes (where CX modal saves notes)
                                     $stopWordsRegex = "ganti |tambah |pasang |repair |jasa |service |dan |pada |bagian |standar";
                                     $sssq->whereRaw('LOWER(cx_issues.resolution_notes) REGEXP REPLACE(REPLACE(REPLACE(LOWER(work_order_services.category_name), "ganti ", ""), "tambah ", ""), "pasang ", "")')
                                          ->orWhereRaw('LOWER(work_orders.technician_notes) REGEXP REPLACE(REPLACE(REPLACE(LOWER(work_order_services.category_name), "ganti ", ""), "tambah ", ""), "pasang ", "")')
                                          ->orWhereRaw('LOWER(work_order_services.category_name) REGEXP REPLACE(REPLACE(REPLACE(LOWER(cx_issues.resolution_notes), " + ", "|"), " ", "|"), ", ", "|")')
                                          ->orWhereRaw('LOWER(work_order_services.custom_service_name) REGEXP REPLACE(REPLACE(REPLACE(LOWER(cx_issues.resolution_notes), " + ", "|"), " ", "|"), ", ", "|")')
                                          ->orWhereRaw('LOWER(work_order_services.category_name) REGEXP REPLACE(REPLACE(REPLACE(LOWER(work_orders.technician_notes), " + ", "|"), " ", "|"), ", ", "|")')
                                          ->orWhereRaw('LOWER(work_order_services.custom_service_name) REGEXP REPLACE(REPLACE(REPLACE(LOWER(work_orders.technician_notes), " + ", "|"), " ", "|"), ", ", "|")');
                                 })
                                 ->where(function($sssq) {
                                     $sssq->whereNull('work_order_services.custom_service_name')
                                          ->orWhere('work_order_services.custom_service_name', 'NOT LIKE', 'OTO:%');
                                 });
                         });
                  });
            })
            ->count();

        return [
            'total' => $totalIssues,
            'open' => $openIssues,
            'progress' => $inProgressIssues,
            'resolved' => $resolvedIssues,
            'cancelled' => $cancelledIssues,
            'resolved_with_upsell' => $resolvedWithUpsell,
            'resolved_no_upsell' => $resolvedIssues - $resolvedWithUpsell,
            'resolution_rate' => $resolutionRate,
            'avg_response_time_hours' => round($avgResponseTime ?? 0, 1)
        ];
    }

    /**
     * Trend Data for Charts
     */
    private function getTrendData(Carbon $start, Carbon $end)
    {
        $incomingTrend = CxIssue::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $resolvedTrend = CxIssue::where('status', 'RESOLVED')
            ->whereBetween('resolved_at', [$start, $end])
            ->whereHas('workOrder', function($q) {
                $q->where('status', '!=', 'BATAL');
            })
            ->selectRaw('DATE(resolved_at) as date, count(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $dataIncoming = [];
        $dataResolved = [];
        
        $current = $start->copy();
        while ($current <= $end) {
            $date = $current->toDateString();
            $labels[] = $current->format('d M');
            $dataIncoming[] = $incomingTrend[$date] ?? 0;
            $dataResolved[] = $resolvedTrend[$date] ?? 0;
            $current->addDay();
        }

        return [
            'labels' => $labels,
            'incoming' => $dataIncoming,
            'resolved' => $dataResolved
        ];
    }

    /**
     * Top Problem Categorization
     */
    private function getTopProblemData(Carbon $start, Carbon $end)
    {
        return CxIssue::whereBetween('created_at', [$start, $end])
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Issue Source Distribution
     */
    private function getIssuesBySource(Carbon $start, Carbon $end)
    {
        return CxIssue::with('workOrder')
            ->whereBetween('cx_issues.created_at', [$start, $end])
            ->join('work_orders', 'cx_issues.work_order_id', '=', 'work_orders.id')
            ->select('work_orders.previous_status as source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->get();
    }

    /**
     * Top Resolver Performance
     */
    private function getTopResolvers(Carbon $start, Carbon $end)
    {
        return CxIssue::with('resolver')
            ->whereNotNull('resolved_by')
            ->whereBetween('resolved_at', [$start, $end])
            ->select('resolved_by', DB::raw('count(*) as resolved_count'))
            ->groupBy('resolved_by')
            ->orderBy('resolved_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Recent Issues Activity
     */
    private function getRecentIssues(Carbon $start, Carbon $end)
    {
        return CxIssue::with(['workOrder.workOrderServices.service', 'workOrder.otos', 'reporter'])
            ->orderBy('updated_at', 'desc')
            ->limit(15)
            ->get();
    }

    /**
     * Overdue Tasks Monitoring
     */
    private function getOverdueIssues()
    {
        return CxIssue::where('status', 'OPEN')
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->with(['workOrder.workOrderServices.service', 'workOrder.otos', 'reporter'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Upsell Metrics (Match 1:1 with Original logic)
     */
    private function getUpsellMetrics(Carbon $start, Carbon $end)
    {
        // 1. Tambah Jasa Aggregation (Manual SPK Services)
        // Focus: non-OTO services matching keywords OR 'Tambah Jasa' trigger in notes.
        $tambahJasaQuery = WorkOrderService::join('work_orders', 'work_order_services.work_order_id', '=', 'work_orders.id')
            ->join('cx_issues', 'work_orders.id', '=', 'cx_issues.work_order_id')
            ->where('cx_issues.status', 'RESOLVED')
            ->whereBetween('cx_issues.resolved_at', [$start, $end])
            ->whereRaw('work_order_services.created_at >= cx_issues.created_at')
            ->where(function($q) {
                // EXCLUDE: Don't count OTO services in the Left widget
                $q->whereNull('work_order_services.custom_service_name')
                  ->orWhere('work_order_services.custom_service_name', 'NOT LIKE', 'OTO:%');
            })
            ->where('cx_issues.resolution_type', 'tambah_jasa')
            ->whereRaw('work_order_services.created_at >= cx_issues.created_at')
            ->where(function($q) {
                // THE GRANULAR KEYWORD LOCK (v4-Fix)
                $q->where(function($noteMatch) {
                    $noteMatch->whereNotNull('cx_issues.resolution_notes')
                             ->where('cx_issues.resolution_notes', '!=', '')
                             ->where(function($inner) {
                                 $inner->whereRaw('LOWER(cx_issues.resolution_notes) REGEXP REPLACE(REPLACE(REPLACE(LOWER(work_order_services.category_name), "ganti ", ""), "tambah ", ""), "pasang ", "")')
                                       ->orWhereRaw('LOWER(work_order_services.category_name) REGEXP REPLACE(REPLACE(REPLACE(LOWER(cx_issues.resolution_notes), " + ", "|"), " ", "|"), ", ", "|")')
                                       ->orWhereRaw('LOWER(work_order_services.custom_service_name) REGEXP REPLACE(REPLACE(REPLACE(LOWER(cx_issues.resolution_notes), " + ", "|"), " ", "|"), ", ", "|")');
                             });
                })
                ->orWhere(function($techMatch) {
                    $techMatch->where(function($emptyNotes) {
                                $emptyNotes->whereNull('cx_issues.resolution_notes')
                                           ->orWhere('cx_issues.resolution_notes', '');
                             })
                             ->where(function($inner) {
                                $inner->whereRaw('LOWER(work_orders.technician_notes) REGEXP REPLACE(REPLACE(REPLACE(LOWER(work_order_services.category_name), "ganti ", ""), "tambah ", ""), "pasang ", "")')
                                      ->orWhereRaw('LOWER(work_order_services.category_name) REGEXP REPLACE(REPLACE(REPLACE(LOWER(work_orders.technician_notes), " + ", "|"), " ", "|"), ", ", "|")')
                                      ->orWhereRaw('LOWER(work_order_services.custom_service_name) REGEXP REPLACE(REPLACE(REPLACE(LOWER(work_orders.technician_notes), " + ", "|"), " ", "|"), ", ", "|")');
                             });
                });
            })
            ->select('work_order_services.*')
            ->groupBy('work_order_services.id');

        $tambahJasaResults = (clone $tambahJasaQuery)->get();
        $totalTambahJasaNominal = $tambahJasaResults->sum('cost');
        $totalSpkTambahJasa = $tambahJasaResults->unique('work_order_id')->count();
        $arpuTambahJasa = $totalSpkTambahJasa > 0 ? $totalTambahJasaNominal / $totalSpkTambahJasa : 0;
        
        $tambahJasaItems = (clone $tambahJasaQuery)
            ->with(['service', 'workOrder'])
            ->select(
                'work_order_services.work_order_id', 
                'work_order_services.category_name', 
                'work_order_services.custom_service_name', 
                'work_order_services.service_id', 
                DB::raw('count(*) as count'), 
                DB::raw('sum(work_order_services.cost) as total_revenue')
            )
            ->groupBy('work_order_services.work_order_id', 'work_order_services.category_name', 'work_order_services.custom_service_name', 'work_order_services.service_id')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // 2. OTO Aggregation (Specific OTO Packages from 'otos' table)
        // Focus: Accepted packages where CX was involved.
        $otosInPeriod = OTO::where('status', 'ACCEPTED')
            ->whereBetween('customer_responded_at', [$start, $end])
            ->where(function($q) {
                $q->whereNotNull('cx_assigned_to')
                  ->orWhereNotNull('cx_contacted_at')
                  ->orWhere('created_by', 1);
            })
            ->get();
            
        // Clean currency format 'Rp. 50.000' to float
        $totalOtoNominal = $otosInPeriod->sum(function($oto) {
            $price = $oto->total_oto_price;
            if (empty($price)) return 0;
            return (float) str_replace(['Rp. ', 'Rp.', '.', ','], '', $price);
        });
        
        $totalSpkOto = $otosInPeriod->unique('work_order_id')->count();
        $arpuOto = $totalSpkOto > 0 ? $totalOtoNominal / $totalSpkOto : 0;
        
        $otoItems = $otosInPeriod->map(function($oto) {
            return (object)[
                'title' => $oto->proposed_services ?: ($oto->title ?: 'Layanan OTO'),
                'spk_number' => $oto->workOrder->spk_number ?? '-',
                'count' => 1,
                'total_revenue' => (float) str_replace(['Rp. ', 'Rp.', '.', ','], '', $oto->total_oto_price)
            ];
        })
        ->sortByDesc('total_revenue')
        ->values();

        return [
            'total_volume' => (int)$totalSpkTambahJasa,
            'total_nominal' => (float)$totalTambahJasaNominal,
            'tambah_jasa_items' => $tambahJasaItems,
            'arpu_tambah_jasa' => (float)$arpuTambahJasa,
            'oto_nominal' => (float)$totalOtoNominal,
            'oto_volume' => (int)$totalSpkOto,
            'oto_items' => $otoItems,
            'arpu_oto' => (float)$arpuOto
        ];
    }
}
