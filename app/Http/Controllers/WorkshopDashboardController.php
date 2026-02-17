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
    /**
     * Dashboard Index Page
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfDay();

        $data = array_merge(
            [
                'filterStartDate' => $startDate->format('Y-m-d'),
                'filterEndDate' => $endDate->format('Y-m-d'),
            ],
            $this->getSnapshotMetrics(),
            $this->getHistoricalMetrics($startDate, $endDate),
            $this->getTrendData($startDate, $endDate),
            $this->getPerformanceData($startDate, $endDate),
            ['matrixData' => (new WorkshopMatrixService())->getMatrixData()]
        );

        return view('workshop.dashboard.index', $data);
    }

    /**
     * Export Analytics Report
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfDay();

        $historical = $this->getHistoricalMetrics($startDate, $endDate);
        $performance = $this->getPerformanceData($startDate, $endDate);
        
        $pdf = Pdf::loadView('reports.workshop-analytics', array_merge([
            'startDate' => $startDate->format('d M Y'),
            'endDate' => $endDate->format('d M Y'),
            'orders' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereDate('finished_date', '>=', $startDate)
                ->whereDate('finished_date', '<=', $endDate)
                ->with(['services', 'qcFinalPic'])
                ->take(50)
                ->get()
        ], $historical, $performance));

        return $pdf->stream('Workshop_Analytics_' . $startDate->format('Ymd') . '.pdf');
    }

    /**
     * Real-time Snapshots
     */
    protected function getSnapshotMetrics()
    {
        $allActiveOrders = WorkOrder::whereIn('status', [
            WorkOrderStatus::ASSESSMENT,
            WorkOrderStatus::PREPARATION,
            WorkOrderStatus::SORTIR,
            WorkOrderStatus::PRODUCTION,
            WorkOrderStatus::QC,
        ])->get();

        $urgentOrders = $allActiveOrders->filter(fn($o) => $o->days_remaining !== null && $o->days_remaining <= 3)->sortBy('days_remaining');

        $workloadByStation = [
            'assessment' => WorkOrder::where('status', WorkOrderStatus::ASSESSMENT)->count(),
            'preparation' => WorkOrder::where('status', WorkOrderStatus::PREPARATION)->count(),
            'sortir' => WorkOrder::where('status', WorkOrderStatus::SORTIR)->count(),
            'production' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION)->count(),
            'qc' => WorkOrder::where('status', WorkOrderStatus::QC)->count(),
        ];

        return [
            'inProgress' => WorkOrder::whereIn('status', [WorkOrderStatus::PREPARATION, WorkOrderStatus::SORTIR, WorkOrderStatus::PRODUCTION, WorkOrderStatus::QC])->count(),
            'urgentCount' => $urgentOrders->count(),
            'urgentOrders' => $urgentOrders,
            'onTimeOrders' => $allActiveOrders->filter(fn($o) => $o->days_remaining > 3)->count(),
            'atRiskOrders' => $allActiveOrders->filter(fn($o) => $o->days_remaining !== null && $o->days_remaining > 0 && $o->days_remaining <= 3)->count(),
            'overdueOrders' => $allActiveOrders->filter(fn($o) => $o->is_overdue)->count(),
            'workloadByStation' => $workloadByStation,
            'bottleneckStation' => collect($workloadByStation)->sortDesc()->keys()->first(),
            'bottleneckCount' => collect($workloadByStation)->sortDesc()->first(),
            'lowStockMaterials' => Material::whereRaw('stock < min_stock')->orderBy('stock', 'asc')->take(5)->get(),
            'technicianLoad' => $this->getTechnicianLoad(),
            'recentLogs' => WorkOrderLog::latest()->take(10)->with(['user', 'workOrder'])->get(),
        ];
    }

    /**
     * Historical Metrics
     */
    protected function getHistoricalMetrics(Carbon $startDate, Carbon $endDate)
    {
        $completedInRange = WorkOrder::where('status', WorkOrderStatus::SELESAI)
            ->whereDate('finished_date', '>=', $startDate)
            ->whereDate('finished_date', '<=', $endDate)
            ->get();

        $throughput = $completedInRange->count();
        $totalDays = $completedInRange->sum(fn($o) => $o->entry_date->diffInDays($o->finished_date));
        $onTimeCount = $completedInRange->filter(fn($o) => $o->finished_date <= $o->estimation_date)->count();

        return [
            'throughput' => $throughput,
            'revenue' => $completedInRange->sum('total_service_price'),
            'avgCompletionTime' => $throughput > 0 ? round($totalDays / $throughput, 1) : 0,
            'onTimeRate' => $throughput > 0 ? round(($onTimeCount / $throughput) * 100) : 0,
            'qcPassRate' => $throughput > 0 ? round(($completedInRange->where('is_revising', false)->count() / $throughput) * 100) : 0,
            'capacityUtilization' => $throughput, // Simplified
        ];
    }

    /**
     * Trend Data
     */
    protected function getTrendData(Carbon $startDate, Carbon $endDate)
    {
        $dailyCompletions = WorkOrder::where('status', WorkOrderStatus::SELESAI)
            ->whereDate('finished_date', '>=', $startDate)
            ->whereDate('finished_date', '<=', $endDate)
            ->selectRaw('DATE(finished_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $data = [];
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $labels[] = $date->format('d M');
            $data[] = $dailyCompletions[$date->format('Y-m-d')] ?? 0;
        }

        return ['trendLabels' => $labels, 'trendData' => $data];
    }

    /**
     * Performance and Mix
     */
    protected function getPerformanceData(Carbon $startDate, Carbon $endDate)
    {
        return [
            'topPerformers' => User::whereHas('qcFinalCompleted', fn($q) => $q->whereDate('qc_final_completed_at', '>=', $startDate)->whereDate('qc_final_completed_at', '<=', $endDate))
                ->withCount(['qcFinalCompleted as completed_count' => fn($q) => $q->whereDate('qc_final_completed_at', '>=', $startDate)->whereDate('qc_final_completed_at', '<=', $endDate)])
                ->orderByDesc('completed_count')->take(5)->get(),
            'serviceMix' => WorkOrderService::whereHas('workOrder', fn($q) => $q->where('status', WorkOrderStatus::SELESAI)->whereDate('finished_date', '>=', $startDate)->whereDate('finished_date', '<=', $endDate))
                ->whereNotNull('service_id')->selectRaw('service_id, SUM(cost) as total_revenue, COUNT(*) as order_count')
                ->groupBy('service_id')->orderByDesc('total_revenue')->take(5)->with('service')->get(),
        ];
    }

    /**
     * Technician Load Helper
     */
    protected function getTechnicianLoad()
    {
        return User::where('role', '!=', 'customer')
            ->get()
            ->map(fn($user) => [
                'name' => $user->name,
                'count' => WorkOrder::whereIn('status', [WorkOrderStatus::PRODUCTION, WorkOrderStatus::QC])
                    ->where(fn($q) => $q->where('technician_production_id', $user->id)->orWhere('qc_final_pic_id', $user->id))
                    ->count()
            ])
            ->where('count', '>', 0)->sortByDesc('count')->take(10)->values();
    }
}
