<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WorkshopMatrixService;
use App\Services\WorkshopMetricsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WorkshopSyncController extends Controller
{
    protected $matrixService;
    protected $metricsService;

    public function __construct(WorkshopMatrixService $matrixService, WorkshopMetricsService $metricsService)
    {
        $this->matrixService = $matrixService;
        $this->metricsService = $metricsService;
    }

    /**
     * Get Workshop Dashboard Sync Data
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        // 1. Core Data
        $matrixData = $this->matrixService->getMatrixData();
        $snapshot = $this->metricsService->getSnapshotMetrics();
        $historical = $this->metricsService->getHistoricalMetrics($startDate, $endDate);

        // 2. Extended Analytics
        $pipeline = $this->metricsService->getPipelineStats();
        $trends = $this->metricsService->getTrendData($startDate, $endDate);
        $workload = $this->metricsService->getWorkloadStats($startDate, $endDate);
        $serviceMix = $this->metricsService->getServiceMix($startDate, $endDate);
        $leaderboard = $this->metricsService->getServiceLeaderboard($startDate, $endDate);

        // 3. Operational Alerts & Feeds
        $urgentOrders = $this->metricsService->getUrgentOrderList();
        $stockAlerts = $this->metricsService->getMaterialAlerts();
        $activityFeed = $this->metricsService->getRecentActivity();

        return response()->json([
            'success' => true,
            'data' => [
                'matrix' => $matrixData['groups'],
                'metrics' => [
                    'snapshot' => $snapshot,
                    'historical' => $historical
                ],
                'analytics' => [
                    'pipeline' => $pipeline,
                    'trends' => $trends,
                    'workload' => $workload,
                    'service_mix' => $serviceMix,
                    'leaderboard' => $leaderboard
                ],
                'operational' => [
                    'urgent_orders' => $urgentOrders,
                    'stock_alerts' => $stockAlerts,
                    'recent_activity' => $activityFeed
                ],
                'summary' => [
                    'total_spk_active' => $matrixData['total_spk'],
                    'period' => [
                        'start' => $startDate->toDateString(),
                        'end' => $endDate->toDateString()
                    ]
                ]
            ],
            'last_sync' => now()->toDateTimeString()
        ]);
    }
}
