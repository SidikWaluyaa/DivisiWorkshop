<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CxIssue;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CxDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date Filter (Normalize to include full days)
        $filterStartDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $filterEndDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        $start = Carbon::parse($filterStartDate)->startOfDay();
        $end = Carbon::parse($filterEndDate)->endOfDay();

        // KPI Metrics
        // Total Issues reported in this period
        $totalIssues = CxIssue::whereBetween('created_at', [$start, $end])->count();
        
        // Current state of those issues (Open/Progress/Resolved/Cancelled)
        $openIssues = CxIssue::where('status', 'OPEN')->whereBetween('created_at', [$start, $end])->count();
        $inProgressIssues = CxIssue::where('status', 'IN_PROGRESS')->whereBetween('created_at', [$start, $end])->count();
        $resolvedIssues = CxIssue::where('status', 'RESOLVED')->whereBetween('resolved_at', [$start, $end])->count();
        
        // Avg Response Time (in hours) - Based on issues resolved in this period
        $avgResponseTime = CxIssue::where('status', 'RESOLVED')
            ->whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$start, $end])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');
        $avgResponseTime = $avgResponseTime ? round($avgResponseTime, 1) : 0;

        // Resolution Rate (Resolved in period / Reported in period)
        $resolutionRate = $totalIssues > 0 ? round(($resolvedIssues / $totalIssues) * 100, 1) : 0;

        // Trend Data (Unified Timeline)
        // 1. Incoming Issues (Created At)
        $incomingTrend = CxIssue::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');

        // 2. Resolved Issues (Resolved At)
        $resolvedTrend = CxIssue::where('status', 'RESOLVED')
            ->whereBetween('resolved_at', [$start, $end])
            ->selectRaw('DATE(resolved_at) as date, count(*) as count')
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');

        // Generate Labels (Every day between start and end)
        $trendLabels = [];
        $trendOpen = []; // Incoming
        $trendResolved = [];
        
        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $trendLabels[] = $current->format('d M');
            $trendOpen[] = $incomingTrend[$dateStr] ?? 0;
            $trendResolved[] = $resolvedTrend[$dateStr] ?? 0;
            $current->addDay();
        }

        // Issue by Category
        $issuesByCategory = CxIssue::whereBetween('created_at', [$start, $end])
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        // Issue by Source (from work_order status when reported)
        $issuesBySource = CxIssue::with('workOrder')
            ->whereBetween('cx_issues.created_at', [$start, $end])
            ->join('work_orders', 'cx_issues.work_order_id', '=', 'work_orders.id')
            ->select('work_orders.previous_status as source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->get();

        // Recent Activity (Sorted by updated_at)
        $recentIssues = CxIssue::with(['workOrder', 'reporter'])
            ->orderBy('updated_at', 'desc')
            ->limit(15)
            ->get();

        // Priority/Overdue Issues (Open > 3 days)
        $overdueIssues = CxIssue::where('status', 'OPEN')
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->with(['workOrder', 'reporter'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Team Performance (Top Resolvers)
        $topResolvers = CxIssue::whereNotNull('resolved_by')
            ->whereBetween('resolved_at', [$start, $end])
            ->select('resolved_by', DB::raw('count(*) as resolved_count'))
            ->with('resolver')
            ->groupBy('resolved_by')
            ->orderBy('resolved_count', 'desc')
            ->limit(5)
            ->get();

        // Common Problems (Top 5 descriptions - simplified)
        $commonProblems = CxIssue::whereBetween('created_at', [$start, $end])
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return view('cx.dashboard.index', compact(
            'filterStartDate',
            'filterEndDate',
            'totalIssues',
            'openIssues',
            'inProgressIssues',
            'resolvedIssues',
            'avgResponseTime',
            'resolutionRate',
            'trendLabels',
            'trendOpen',
            'trendResolved',
            'issuesByCategory',
            'issuesBySource',
            'recentIssues',
            'overdueIssues',
            'topResolvers',
            'commonProblems'
        ));
    }

    /**
     * API endpoint for realtime polling (JSON).
     */
    public function apiStats(Request $request)
    {
        $filterStartDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $filterEndDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $start = Carbon::parse($filterStartDate)->startOfDay();
        $end = Carbon::parse($filterEndDate)->endOfDay();

        $totalIssues = CxIssue::whereBetween('created_at', [$start, $end])->count();
        $openIssues = CxIssue::where('status', 'OPEN')->whereBetween('created_at', [$start, $end])->count();
        $inProgressIssues = CxIssue::where('status', 'IN_PROGRESS')->whereBetween('created_at', [$start, $end])->count();
        $resolvedIssues = CxIssue::where('status', 'RESOLVED')->whereBetween('resolved_at', [$start, $end])->count();

        $avgResponseTime = CxIssue::where('status', 'RESOLVED')
            ->whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$start, $end])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');
        $avgResponseTime = $avgResponseTime ? round($avgResponseTime, 1) : 0;

        $resolutionRate = $totalIssues > 0 ? round(($resolvedIssues / $totalIssues) * 100, 1) : 0;

        $overdueCount = CxIssue::where('status', 'OPEN')
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->count();

        return response()->json([
            'total_issues' => $totalIssues,
            'open_issues' => $openIssues,
            'in_progress_issues' => $inProgressIssues,
            'resolved_issues' => $resolvedIssues,
            'avg_response_time' => $avgResponseTime,
            'resolution_rate' => $resolutionRate,
            'overdue_count' => $overdueCount,
            'timestamp' => now()->format('H:i:s'),
        ]);
    }
}
