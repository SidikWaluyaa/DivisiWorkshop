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
        // Date Filter
        $filterStartDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $filterEndDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // KPI Metrics
        $totalIssues = CxIssue::whereBetween('created_at', [$filterStartDate, $filterEndDate])->count();
        $openIssues = CxIssue::where('status', 'OPEN')->count();
        $inProgressIssues = CxIssue::where('status', 'IN_PROGRESS')->count();
        $resolvedIssues = CxIssue::where('status', 'RESOLVED')->whereBetween('resolved_at', [$filterStartDate, $filterEndDate])->count();
        
        // Avg Response Time (in hours)
        $avgResponseTime = CxIssue::whereNotNull('resolved_at')
            ->whereBetween('created_at', [$filterStartDate, $filterEndDate])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');
        $avgResponseTime = $avgResponseTime ? round($avgResponseTime, 1) : 0;

        // Resolution Rate
        $resolutionRate = $totalIssues > 0 ? round(($resolvedIssues / $totalIssues) * 100, 1) : 0;

        // Trend Data (Daily for last 30 days or filtered range)
        $trendData = CxIssue::whereBetween('created_at', [$filterStartDate, $filterEndDate])
            ->selectRaw('DATE(created_at) as date, 
                         SUM(CASE WHEN status = "OPEN" THEN 1 ELSE 0 END) as open_count,
                         SUM(CASE WHEN status = "IN_PROGRESS" THEN 1 ELSE 0 END) as progress_count,
                         SUM(CASE WHEN status = "RESOLVED" THEN 1 ELSE 0 END) as resolved_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $trendLabels = $trendData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'));
        $trendOpen = $trendData->pluck('open_count');
        $trendProgress = $trendData->pluck('progress_count');
        $trendResolved = $trendData->pluck('resolved_count');

        // Issue by Category
        $issuesByCategory = CxIssue::whereBetween('created_at', [$filterStartDate, $filterEndDate])
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        // Issue by Source (from work_order status when reported)
        $issuesBySource = CxIssue::with('workOrder')
            ->whereBetween('cx_issues.created_at', [$filterStartDate, $filterEndDate])
            ->join('work_orders', 'cx_issues.work_order_id', '=', 'work_orders.id')
            ->select('work_orders.previous_status as source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->get();

        // Recent Issues
        $recentIssues = CxIssue::with(['workOrder', 'reporter'])
            ->orderBy('created_at', 'desc')
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
            ->whereBetween('resolved_at', [$filterStartDate, $filterEndDate])
            ->select('resolved_by', DB::raw('count(*) as resolved_count'))
            ->with('resolver')
            ->groupBy('resolved_by')
            ->orderBy('resolved_count', 'desc')
            ->limit(5)
            ->get();

        // Common Problems (Top 5 descriptions - simplified)
        $commonProblems = CxIssue::whereBetween('created_at', [$filterStartDate, $filterEndDate])
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
            'trendProgress',
            'trendResolved',
            'issuesByCategory',
            'issuesBySource',
            'recentIssues',
            'overdueIssues',
            'topResolvers',
            'commonProblems'
        ));
    }
}
