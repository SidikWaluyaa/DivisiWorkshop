<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WarehouseDashboardApiService;
use App\Http\Resources\V1\WarehouseDashboardSummaryResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WarehouseDashboardApiController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseDashboardApiService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     * Get warehouse dashboard summary
     * 
     * @param Request $request
     * @return WarehouseDashboardSummaryResource
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(7)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $search = $request->search;

        $summaryData = $this->warehouseService->getWarehouseSummary($startDate, $endDate, $search);

        return new WarehouseDashboardSummaryResource($summaryData);
    }
}
