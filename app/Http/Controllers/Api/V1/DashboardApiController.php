<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardApiService;
use App\Http\Resources\V1\DashboardSummaryResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardApiController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardApiService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get dashboard summary
     * 
     * @param Request $request
     * @return DashboardSummaryResource
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $forceRefresh = $request->boolean('force_refresh', false);

        $summaryData = $this->dashboardService->getDashboardSummary($startDate, $endDate, $forceRefresh);

        return new DashboardSummaryResource($summaryData);
    }
}
