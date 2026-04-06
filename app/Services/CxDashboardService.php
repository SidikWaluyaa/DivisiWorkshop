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
        $cacheKey = "cx_dashboard_summary_v8_{$start->format('Ymd')}_{$end->format('Ymd')}";

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

        // Resolved Breakdown logic - Updated with Atomic PHP Matching (v7/v8)
        $resolvedDocs = CxIssue::where('status', 'RESOLVED')
            ->where('cx_issues.resolution_type', 'tambah_jasa')
            ->whereBetween('resolved_at', [$start, $end])
            ->with(['workOrder.workOrderServices'])
            ->get();
            
        $resolvedWithUpsell = 0;
        foreach ($resolvedDocs as $issue) {
            $wo = $issue->workOrder;
            if (!$wo || $wo->status === 'BATAL') continue;
            
            $hasValidUpsell = false;
            foreach ($wo->workOrderServices as $svc) {
                if ($this->isServiceMatchIssue($svc, $issue)) {
                    $hasValidUpsell = true;
                    break;
                }
            }
            
            if ($hasValidUpsell) {
                $resolvedWithUpsell++;
            }
        }

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
     * THE ATOMIC MATCHING LOGIC (Identical to AuditPrecision v7)
     */
    private function isServiceMatchIssue($service, $issue)
    {
        // 1. Timestamp Lock: Service MUST be created after/during the issue
        if ($service->created_at < $issue->created_at) return false;
        
        // 2. OTO Exclusion
        if (!empty($service->custom_service_name) && str_starts_with($service->custom_service_name, 'OTO:')) return false;
        
        // 3. Keyword Lock: Triple-Checking words
        $notes = strtolower($issue->resolution_notes);
        $techNotes = $issue->workOrder ? strtolower($issue->workOrder->technician_notes) : '';
        $cat = strtolower($service->category_name);
        $custom = strtolower($service->custom_service_name);
        
        // Stop words for matching
        $stopWords = ['ganti', 'tambah', 'pasang', 'repair', 'jasa', 'service', 'dan', 'pada', 'bagian', 'standar', 'reparasi'];
        
        $checkMatch = function($targetNote) use ($stopWords, $cat, $custom) {
            if (empty($targetNote)) return false;
            $noteWords = preg_split('/[\s,\+\.]+/', $targetNote, -1, PREG_SPLIT_NO_EMPTY);
            foreach($noteWords as $nw) {
                if (in_array($nw, $stopWords)) continue;
                if (strlen($nw) > 2) {
                    // Precise word-in-word check
                    if (!empty($cat) && (str_contains($cat, $nw) || str_contains($nw, $cat))) {
                        // Prevent "sol" matching "midsole"
                        if ($nw === 'sol' && str_contains($cat, 'midsole') && !str_contains($cat, ' sol ')) return false;
                        return true;
                    }
                    if (!empty($custom) && (str_contains($custom, $nw) || str_contains($nw, $cat))) return true;
                }
            }
            return false;
        };

        if (!empty($notes) && $notes !== '') {
            return $checkMatch($notes);
        } else {
            return $checkMatch($techNotes);
        }
    }

    /**
     * Upsell Metrics (Atomic Parity + OTO Monitoring Version v8)
     */
    private function getUpsellMetrics(Carbon $start, Carbon $end)
    {
        // 1. TAMBAH JASA (Manual)
        $issues = CxIssue::where('status', 'RESOLVED')
            ->where('resolution_type', 'tambah_jasa')
            ->whereBetween('resolved_at', [$start, $end])
            ->with(['workOrder.workOrderServices'])
            ->get();
            
        $upsellServices = collect();
        foreach ($issues as $issue) {
            $wo = $issue->workOrder;
            if (!$wo || $wo->status === 'BATAL') continue;
            foreach ($wo->workOrderServices as $svc) {
                if ($this->isServiceMatchIssue($svc, $issue)) {
                    $svc->parent_issue_spk = $wo->spk_number;
                    $upsellServices->push($svc);
                }
            }
        }
        
        $totalNominal = $upsellServices->sum('cost');
        $totalSpk = $upsellServices->unique('work_order_id')->count();
        
        $tambahJasaItems = $upsellServices->groupBy(function($s) {
            return $s->parent_issue_spk . '_' . ($s->custom_service_name ?: $s->category_name);
        })->map(function($group) {
            $first = $group->first();
            return (object)[
                'work_order_id' => $first->work_order_id,
                'spk_number' => $first->parent_issue_spk,
                'category_name' => $first->category_name,
                'custom_service_name' => $first->custom_service_name,
                'count' => $group->count(),
                'total_revenue' => $group->sum('cost')
            ];
        })->sortByDesc('total_revenue')->values();

        // 2. OTO MONITORING (v8 Logic)
        // Switch to created_at to track ACTIVE prospection
        $otosInPeriod = OTO::whereBetween('created_at', [$start, $end])
            ->with('workOrder')
            ->get();
            
        // Volume: All active OTOs in the period
        $totalSpkOto = $otosInPeriod->unique('work_order_id')->count();
        
        // Revenue: Total Prospect Nominal (v9 - All active)
        $totalOtoNominal = $otosInPeriod->sum(function($oto) {
            $price = $oto->total_oto_price;
            if (empty($price)) return 0;
            return (float) str_replace(['Rp. ', 'Rp.', '.', ','], '', $price);
        });
        
        $otoItems = $otosInPeriod->map(function($oto) {
            $statusLabel = $oto->status;
            if ($oto->status === 'ACCEPTED') $statusLabel = '✅ DEAL';
            if ($oto->status === 'PENDING_CX') $statusLabel = '⏱️ CX PROCESS';
            if ($oto->status === 'PENDING_CUSTOMER') $statusLabel = '💬 WAIT CUSTOMER';

            return (object)[
                'title' => ($oto->proposed_services ?: ($oto->title ?: 'Layanan OTO')) . " [" . $statusLabel . "]",
                'spk_number' => $oto->workOrder->spk_number ?? '-',
                'count' => 1,
                'total_revenue' => (float) str_replace(['Rp. ', 'Rp.', '.', ','], '', $oto->total_oto_price)
            ];
        })->sortByDesc('total_revenue')->values();

        return [
            'total_volume' => (int)$totalSpk,
            'total_nominal' => (float)$totalNominal,
            'tambah_jasa_items' => $tambahJasaItems,
            'arpu_tambah_jasa' => $totalSpk > 0 ? $totalNominal / $totalSpk : 0,
            'oto_nominal' => (float)$totalOtoNominal,
            'oto_volume' => (int)$totalSpkOto,
            'oto_items' => $otoItems,
            'arpu_oto' => $totalSpkOto > 0 ? $totalOtoNominal / $totalSpkOto : 0
        ];
    }

    private function getTrendData(Carbon $start, Carbon $end)
    {
        $incomingTrend = CxIssue::whereBetween('created_at', [$start, $end])->selectRaw('DATE(created_at) as date, count(*) as count')->groupBy('date')->pluck('count', 'date');
        $resolvedTrend = CxIssue::where('status', 'RESOLVED')->whereBetween('resolved_at', [$start, $end])->whereHas('workOrder', function($q){$q->where('status','!=','BATAL');})->selectRaw('DATE(resolved_at) as date, count(*) as count')->groupBy('date')->pluck('count', 'date');
        $labels = []; $dataIncoming = []; $dataResolved = [];
        $current = $start->copy();
        while ($current <= $end) {
            $date = $current->toDateString();
            $labels[] = $current->format('d M');
            $dataIncoming[] = $incomingTrend[$date] ?? 0;
            $dataResolved[] = $resolvedTrend[$date] ?? 0;
            $current->addDay();
        }
        return ['labels' => $labels, 'incoming' => $dataIncoming, 'resolved' => $dataResolved];
    }
    private function getTopProblemData(Carbon $start, Carbon $end) { return CxIssue::whereBetween('created_at', [$start, $end])->select('category', DB::raw('count(*) as count'))->groupBy('category')->orderBy('count', 'desc')->limit(5)->get(); }
    private function getIssuesBySource(Carbon $start, Carbon $end) { return CxIssue::with('workOrder')->whereBetween('cx_issues.created_at', [$start, $end])->join('work_orders', 'cx_issues.work_order_id', '=', 'work_orders.id')->select('work_orders.previous_status as source', DB::raw('count(*) as count'))->groupBy('source')->get(); }
    private function getTopResolvers(Carbon $start, Carbon $end) { return CxIssue::with('resolver')->whereNotNull('resolved_by')->whereBetween('resolved_at', [$start, $end])->select('resolved_by', DB::raw('count(*) as resolved_count'))->groupBy('resolved_by')->orderBy('resolved_count', 'desc')->limit(5)->get(); }
    private function getRecentIssues(Carbon $start, Carbon $end) { return CxIssue::with(['workOrder.workOrderServices.service', 'workOrder.otos', 'reporter'])->orderBy('updated_at', 'desc')->limit(15)->get(); }
    private function getOverdueIssues() { return CxIssue::where('status', 'OPEN')->where('created_at', '<', Carbon::now()->subDays(3))->with(['workOrder.workOrderServices.service', 'workOrder.otos', 'reporter'])->orderBy('created_at', 'asc')->get(); }
}
