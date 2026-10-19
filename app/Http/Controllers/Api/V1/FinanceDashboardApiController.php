<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\FinanceDashboardApiService;
use App\Services\KamusMetrikQuery;
use App\Http\Resources\V1\FinanceDashboardSummaryResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinanceDashboardApiController extends Controller
{
    protected $financeDashboardService;

    public function __construct(FinanceDashboardApiService $financeDashboardService)
    {
        $this->financeDashboardService = $financeDashboardService;
    }

    /**
     * Get finance dashboard summary metrics
     * 
     * @param Request $request
     * @return FinanceDashboardSummaryResource
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth()->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $summaryData = $this->financeDashboardService->getFinanceSummary($startDate, $endDate);

        // Kamus Metrik v2.0: red alert kebocoran kas. Dihitung di sini (bukan di service) agar halaman
        // Dashboard & laporan Finance ERP yang memakai getFinanceSummary() tidak mendapat query tambahan.
        $redAlerts = KamusMetrikQuery::redAlerts($startDate, $endDate);
        $summaryData['red_alerts'] = $redAlerts['items'];
        $summaryData['red_alerts_ringkasan'] = $redAlerts['ringkasan'];

        return new FinanceDashboardSummaryResource($summaryData);
    }
}
