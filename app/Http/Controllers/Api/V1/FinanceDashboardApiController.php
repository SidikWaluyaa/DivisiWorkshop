<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\FinanceDashboardApiService;
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

        return new FinanceDashboardSummaryResource($summaryData);
    }
}
