<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CxIssue;
use App\Services\CxDashboardService;
use Carbon\Carbon;

class CxDashboardController extends Controller
{
    protected $cxService;

    public function __construct(CxDashboardService $cxService)
    {
        $this->cxService = $cxService;
    }

    public function index(Request $request)
    {
        // Date Filter
        $filterStartDate = $request->input('start_date', Carbon::now()->format('Y-m-d'));
        $filterEndDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        $start = Carbon::parse($filterStartDate)->startOfDay();
        $end = Carbon::parse($filterEndDate)->endOfDay();
        
        $forceRefresh = $request->has('refresh');

        // Fetch Metrics from the Restored Service (1:1 with original logic)
        $summary = $this->cxService->getSummary($start, $end, $forceRefresh);
        
        $kpi = $summary['kpi'];
        $upsell = $summary['upsell'];
        $trend = $summary['trend'];

        return view('cx.dashboard.index', [
            // KPI Data (Restored Parity)
            'totalIssues' => $kpi['total'],
            'openIssues' => $kpi['open'],
            'inProgressIssues' => $kpi['progress'],
            'resolvedIssues' => $kpi['resolved'],
            'cancelledIssues' => $kpi['cancelled'],
            'resolvedWithUpsell' => $kpi['resolved_with_upsell'],
            'resolvedNoUpsell' => $kpi['resolved_no_upsell'],
            'avgResponseTime' => $kpi['avg_response_time_hours'],
            'resolutionRate' => $kpi['resolution_rate'],
            
            // Upsell / Tambah Jasa Data
            'totalTambahJasaNominal' => $upsell['total_nominal'],
            'totalSpkTambahJasa' => $upsell['total_volume'],
            'arpuTambahJasa' => $upsell['arpu_tambah_jasa'],
            'tambahJasaItems' => $upsell['tambah_jasa_items'],
            
            // OTO Data (Fixed Rp0 with String Parsing)
            'totalOtoNominal' => $upsell['oto_nominal'],
            'totalSpkOto' => $upsell['oto_volume'],
            'arpuOto' => $upsell['arpu_oto'],
            'otoItems' => $upsell['oto_items'],
            
            // Trend Data
            'trendLabels' => $trend['labels'],
            'trendOpen' => $trend['incoming'],
            'trendResolved' => $trend['resolved'],
            
            // Operational & Activity Data
            'issuesByCategory' => $summary['problems'],
            'issuesBySource' => $summary['source'],
            'topResolvers' => $summary['resolvers'],
            'recentIssues' => $summary['recent'],
            'overdueIssues' => $summary['overdue'],
            'commonProblems' => $summary['problems'],
            
            // Filter Data
            'filterStartDate' => $filterStartDate,
            'filterEndDate' => $filterEndDate,
        ]);
    }

    /**
     * API for Dashboard Realtime Polling
     */
    public function apiStats(Request $request)
    {
        $start = $request->has('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->has('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        
        // Always force refresh for API polling to ensure "instant" updates
        $summary = $this->cxService->getSummary($start, $end, true);
        $kpi = $summary['kpi'];
        $upsell = $summary['upsell'];

        return response()->json([
            'total_issues' => $kpi['total'],
            'open_issues' => $kpi['open'],
            'in_progress_issues' => $kpi['progress'],
            'resolved_issues' => $kpi['resolved'],
            'cancelled_issues' => $kpi['cancelled'],
            'avg_response_time' => $kpi['avg_response_time_hours'],
            'resolution_rate' => $kpi['resolution_rate'],
            'total_tambah_jasa' => $upsell['total_nominal'],
            'vol_tambah_jasa' => $upsell['total_volume'],
            'arpu_tambah_jasa' => $upsell['arpu_tambah_jasa'],
            'total_oto' => $upsell['oto_nominal'],
            'vol_oto' => $upsell['oto_volume'],
            'arpu_oto' => $upsell['arpu_oto'],
            'timestamp' => now()->format('H:i:s')
        ]);
    }
}
