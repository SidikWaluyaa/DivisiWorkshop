<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Material;
use App\Models\User;
use App\Models\WorkOrderLog;
use App\Models\WorkOrderService;
use App\Services\WorkshopMatrixService;
use Barryvdh\DomPDF\Facade\Pdf;

class WorkshopDashboardController extends Controller
{
    public function index(Request $request)
    {
        // ========================================
        // 0. DATE FILTER HANDLING
        // ========================================
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfDay();
        
        // Pass filter to view
        $filterStartDate = $startDate->format('Y-m-d');
        $filterEndDate = $endDate->format('Y-m-d');

        // ========================================
        // PHASE 1: Real-time Snapshots (Not affected by date filter)
        // ========================================
        
        // Total orders in workshop (Prep, Sortir, Produksi, QC only - excluding Assessment)
        $inProgress = WorkOrder::whereIn('status', [
            WorkOrderStatus::PREPARATION,
            WorkOrderStatus::SORTIR,
            WorkOrderStatus::PRODUCTION,
            WorkOrderStatus::QC,
        ])->count();
        
        // Urgent orders (deadline <= 3 days)
        $allActiveOrders = WorkOrder::whereIn('status', [
            WorkOrderStatus::ASSESSMENT,
            WorkOrderStatus::PREPARATION,
            WorkOrderStatus::SORTIR,
            WorkOrderStatus::PRODUCTION,
            WorkOrderStatus::QC,
        ])->get();
        
        $urgentOrders = $allActiveOrders->filter(function($order) {
            return $order->days_remaining !== null && $order->days_remaining <= 3;
        })->sortBy('days_remaining');
        
        $urgentCount = $urgentOrders->count();

        // Deadline Distribution (Snapshot)
        $onTimeOrders = $allActiveOrders->filter(fn($o) => $o->days_remaining > 3)->count();
        $atRiskOrders = $allActiveOrders->filter(fn($o) => $o->days_remaining !== null && $o->days_remaining > 0 && $o->days_remaining <= 3)->count();
        $overdueOrders = $allActiveOrders->filter(fn($o) => $o->is_overdue)->count();
        
        // Workload by station (Snapshot)
        $workloadByStation = [
            'assessment' => WorkOrder::where('status', WorkOrderStatus::ASSESSMENT)->count(),
            'preparation' => WorkOrder::where('status', WorkOrderStatus::PREPARATION)->count(),
            'sortir' => WorkOrder::where('status', WorkOrderStatus::SORTIR)->count(),
            'production' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION)->count(),
            'qc' => WorkOrder::where('status', WorkOrderStatus::QC)->count(),
        ];
        
        // Bottleneck Detection
        $bottleneckStation = collect($workloadByStation)->sortDesc()->keys()->first();
        $bottleneckCount = collect($workloadByStation)->sortDesc()->first();

        // Material Stock Alerts
        $lowStockMaterials = Material::whereRaw('stock < min_stock')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // [NEW] Active Load per Technician (Snapshot)
        // Counting orders currently assigned to technicians in Production/QC
        // Note: This requires correct foreign keys. Assuming production relations exist.
        $technicianLoad = User::where('role', '!=', 'customer') // Assuming generic filter, refine if 'technician' role exists
            ->withCount([
                'jobsProduction as active_production',
                'jobsQcFinal as active_qc'
            ])
            ->get()
            ->map(function($user) {
                // Sum all active jobs (you might need to refine status check if assignments persist after completion)
                // Here assuming relation only holds active assignments or we filter by status manually if needed.
                // For simplicity, let's assume 'technician_production_id' implies current assignment.
                // Better approach: Join with WorkOrder and filter status
                $activeCount = WorkOrder::whereIn('status', [WorkOrderStatus::PRODUCTION, WorkOrderStatus::QC])
                    ->where(function($q) use ($user) {
                        $q->where('technician_production_id', $user->id)
                          ->orWhere('qc_final_pic_id', $user->id);
                    })->count();
                
                return [
                    'name' => $user->name,
                    'count' => $activeCount
                ];
            })
            ->where('count', '>', 0)
            ->sortByDesc('count')
            ->take(10)
            ->values();

        // [NEW] Recent Activity Logs
        $recentLogs = WorkOrderLog::latest()
            ->take(10)
            ->with(['user', 'workOrder'])
            ->get();

        // ========================================
        // PHASE 2 & 3: Historical Metrics (AFFECTED by Date Filter)
        // ========================================
        
        // Orders Completed within Range
        $completedInRange = WorkOrder::where('status', WorkOrderStatus::SELESAI)
            ->whereDate('finished_date', '>=', $startDate)
            ->whereDate('finished_date', '<=', $endDate)
            ->get(); // Fetch collection for calculation
            
        $throughput = $completedInRange->count();
        
        // Revenue within Range
        $revenue = $completedInRange->sum('total_service_price');
        
        // Avg Completion Time within Range
        $avgCompletionTime = 0;
        if ($throughput > 0) {
            $totalDays = $completedInRange->sum(function($order) {
                return $order->entry_date->diffInDays($order->finished_date);
            });
            $avgCompletionTime = round($totalDays / $throughput, 1);
        }
        
        // On-Time Rate within Range
        $onTimeCount = $completedInRange->filter(function($order) {
            return $order->finished_date <= $order->estimation_date;
        })->count();
        $onTimeRate = $throughput > 0 ? round(($onTimeCount / $throughput) * 100) : 0;
        
        // QC Pass Rate within Range
        // Assuming 'is_revising' flag sticks. Better if we have log of QC failures within date.
        // Using existing logic:
        $passedFirstTime = $completedInRange->where('is_revising', false)->count();
        $qcPassRate = $throughput > 0 ? round(($passedFirstTime / $throughput) * 100) : 0;

        // Completion Trend (Daily breakdown within range)
        $trendLabels = [];
        $trendData = [];
        
        // If range > 31 days, group by week? For now keeps daily but restrict query if needed.
        // Let's iterate through days in range (careful with long ranges)
        $period = CarbonPeriod::create($startDate, $endDate);
        if ($startDate->diffInDays($endDate) > 60) {
            // Group by month if too long? Let's stick to daily/weekly logic later.
            // For now, simplify to last 7 days IF specific range not set, else daily.
            // Or just limit to chart display points.
        }
        
        // Optimization: SQL Group By
        $dailyCompletions = WorkOrder::where('status', WorkOrderStatus::SELESAI)
            ->whereDate('finished_date', '>=', $startDate)
            ->whereDate('finished_date', '<=', $endDate)
            ->selectRaw('DATE(finished_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');
            
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $trendLabels[] = $date->format('d M');
            $trendData[] = $dailyCompletions[$dateStr] ?? 0;
        }

        // Top Performers within Range
        $topPerformers = User::whereHas('qcFinalCompleted', function($q) use ($startDate, $endDate) {
            $q->whereDate('qc_final_completed_at', '>=', $startDate)
              ->whereDate('qc_final_completed_at', '<=', $endDate);
        })
        ->withCount(['qcFinalCompleted as completed_count' => function($q) use ($startDate, $endDate) {
            $q->whereDate('qc_final_completed_at', '>=', $startDate)
              ->whereDate('qc_final_completed_at', '<=', $endDate);
        }])
        ->orderByDesc('completed_count')
        ->take(5)
        ->get();

        // Active Capacity Metric (Simulated "Weekly Capacity" logic adjusted to range average or sum)
        // Let's show Capacity Utilization based on a standard (e.g. 50 orders/week)
        $capacityUtilization = $throughput; // Just raw number for now

        // Service Mix within Range
        $serviceMix = WorkOrderService::whereHas('workOrder', function($q) use ($startDate, $endDate) {
            $q->where('status', WorkOrderStatus::SELESAI)
                ->whereDate('finished_date', '>=', $startDate)
                ->whereDate('finished_date', '<=', $endDate);
        })
        ->selectRaw('service_id, SUM(cost) as total_revenue, COUNT(*) as order_count')
        ->groupBy('service_id')
        ->orderByDesc('total_revenue')
        ->take(5)
        ->with('service')
        ->get();
        
        // [NEW] PHASE 5: Matrix Dashboard
        $matrixService = new WorkshopMatrixService();
        $matrixData = $matrixService->getMatrixData();
        
        return view('workshop.dashboard.index', compact(
            'filterStartDate',
            'filterEndDate',
            'inProgress',
            'throughput', // Renamed from dailyThroughput
            'urgentCount',
            'urgentOrders',
            'qcPassRate',
            'onTimeOrders',
            'atRiskOrders',
            'overdueOrders',
            'workloadByStation',
            'avgCompletionTime',
            'onTimeRate',
            'trendLabels',
            'trendData',
            'topPerformers',
            'lowStockMaterials',
            'bottleneckStation',
            'bottleneckCount',
            'revenue', // Renamed from revenueThisMonth
            'capacityUtilization', // Renamed from weeklyCapacity
            'serviceMix',
            'technicianLoad',
            'recentLogs',
            'matrixData' // Pass Matrix Data
        ));
    }

    public function export(Request $request) 
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfDay();

        // 1. Fetch Metrics Data
        $completedInRange = WorkOrder::where('status', WorkOrderStatus::SELESAI)
            ->whereDate('finished_date', '>=', $startDate)
            ->whereDate('finished_date', '<=', $endDate)
            ->with(['services', 'qcFinalPic'])
            ->get();

        $throughput = $completedInRange->count();
        $revenue = $completedInRange->sum('total_service_price');
        
        $avgCompletionTime = 0;
        if ($throughput > 0) {
            $totalDays = $completedInRange->sum(fn($o) => $o->entry_date->diffInDays($o->finished_date));
            $avgCompletionTime = round($totalDays / $throughput, 1);
        }

        $qcPassRate = 0;
        if ($throughput > 0) {
            $passedFirstTime = $completedInRange->where('is_revising', false)->count();
            $qcPassRate = round(($passedFirstTime / $throughput) * 100);
        }

        // 2. Top Performers
        $topPerformers = User::whereHas('qcFinalCompleted', function($q) use ($startDate, $endDate) {
            $q->whereDate('qc_final_completed_at', '>=', $startDate)
              ->whereDate('qc_final_completed_at', '<=', $endDate);
        })
        ->withCount(['qcFinalCompleted as completed_count' => function($q) use ($startDate, $endDate) {
            $q->whereDate('qc_final_completed_at', '>=', $startDate)
              ->whereDate('qc_final_completed_at', '<=', $endDate);
        }])
        ->orderByDesc('completed_count')
        ->take(5)
        ->get();

        // 3. Service Mix
        $serviceMix = WorkOrderService::whereHas('workOrder', function($q) use ($startDate, $endDate) {
            $q->where('status', WorkOrderStatus::SELESAI)
                ->whereDate('finished_date', '>=', $startDate)
                ->whereDate('finished_date', '<=', $endDate);
        })
        ->selectRaw('service_id, SUM(cost) as total_revenue, COUNT(*) as order_count')
        ->groupBy('service_id')
        ->orderByDesc('total_revenue')
        ->take(5)
        ->with('service')
        ->get();

        // 4. Generate PDF
        $pdf = Pdf::loadView('reports.workshop-analytics', [
            'startDate' => $startDate->format('d M Y'),
            'endDate' => $endDate->format('d M Y'),
            'throughput' => $throughput,
            'revenue' => $revenue,
            'avgCompletionTime' => $avgCompletionTime,
            'qcPassRate' => $qcPassRate,
            'topPerformers' => $topPerformers,
            'serviceMix' => $serviceMix,
            'orders' => $completedInRange->take(50) // Limit to 50 for PDF readability
        ]);

        return $pdf->stream('Workshop_Analytics_' . $startDate->format('Ymd') . '.pdf');
    }
}
