<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WorkshopMatrixService;
use App\Services\WorkshopMetricsService;
use App\Services\WorkshopMomQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        // Kamus MoM Workshop: blok tambahan hanya bila as_of_date dikirim. Divalidasi sebelum query lama berjalan.
        $asOf = null;
        if ($request->filled('as_of_date')) {
            $asOf = $this->parseAsOfDate((string) $request->as_of_date);
            if ($asOf === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'as_of_date harus berformat Y-m-d dan tidak boleh melewati hari ini.',
                ], 422);
            }
            // Salinan agar method lama yang memodifikasi Carbon (mis. ->endOfDay()) tidak memengaruhi blok MoM.
            $momStart = $startDate->copy();
            $momEnd = $endDate->copy();
        }

        // 1. Core Data
        $matrixData = $this->matrixService->getMatrixData();
        $snapshot = $this->metricsService->getSnapshotMetrics();
        $historical = $this->metricsService->getHistoricalMetrics($startDate, $endDate);

        // 2. Extended Analytics
        $pipeline = $this->metricsService->getPipelineStats($startDate, $endDate);
        $trends = $this->metricsService->getTrendData($startDate, $endDate);
        $workload = $this->metricsService->getWorkloadStats($startDate, $endDate);
        $serviceMix = $this->metricsService->getServiceMix($startDate, $endDate);
        $leaderboard = $this->metricsService->getServiceLeaderboard($startDate, $endDate);

        // 3. Operational Alerts & Feeds
        $urgentOrders = $this->metricsService->getUrgentOrderList($startDate, $endDate);
        $stockAlerts = $this->metricsService->getMaterialAlerts();
        $activityFeed = $this->metricsService->getRecentActivity($startDate, $endDate);

        $payload = [
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
        ];

        if ($asOf !== null) {
            $payload['data']['workshop_mom'] = $this->momBlock($momStart, $momEnd, $asOf);
        }

        return response()->json($payload);
    }

    /** as_of_date (Y-m-d, WIB) → 23:59:59 hari itu; hari ini → saat ini. Null bila tidak valid. */
    private function parseAsOfDate(string $value): ?Carbon
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        try {
            $date = Carbon::createFromFormat('!Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
        if ($date->format('Y-m-d') !== $value) {
            return null; // mis. 2026-02-30
        }
        if ($date->isSameDay(now())) {
            return now();
        }

        return $date->lt(now()->startOfDay()) ? $date->endOfDay() : null;
    }

    /**
     * Blok MoM Workshop, di-cache: 24 jam bila as_of dan end_date sudah lewat, 10 menit bila menyentuh hari ini.
     */
    private function momBlock(Carbon $start, Carbon $end, Carbon $asOf): array
    {
        $today = now()->startOfDay();
        $isPast = $asOf->lt($today) && $end->lt($today);
        $key = 'workshop_mom:v1:'.md5(implode('|', [
            $start->toDateTimeString(),
            $end->toDateTimeString(),
            $asOf->toDateString(),
        ]));

        return Cache::remember($key, $isPast ? 86400 : 600, fn () => [
            'as_of' => $asOf->toDateTimeString(),
            'periode' => WorkshopMomQuery::periode($start, $end),
            'posisi' => WorkshopMomQuery::posisi($asOf),
        ]);
    }
}
