<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WarehouseDashboardApiService;
use App\Http\Resources\V1\WarehouseSortirSummaryResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WarehouseSortirDashboardApiController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseDashboardApiService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     * Get warehouse sortir summary
     * 
     * @param Request $request
     * @return WarehouseSortirSummaryResource
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(7)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $search = $request->search;
        $overdueOnly = $request->boolean('overdue_only', false);

        $summaryData = $this->warehouseService->getSortirSummary($startDate, $endDate, $search, $overdueOnly);

        return new WarehouseSortirSummaryResource($summaryData);
    }
}
