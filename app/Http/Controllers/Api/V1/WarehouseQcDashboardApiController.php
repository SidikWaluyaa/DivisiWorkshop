<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WarehouseDashboardApiService;
use App\Http\Resources\V1\WarehouseQcSummaryResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WarehouseQcDashboardApiController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseDashboardApiService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     * Get warehouse QC summary
     * 
     * @param Request $request
     * @return WarehouseQcSummaryResource
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(7)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $search = $request->search;
        $filter = $request->input('filter', 'all');

        $summaryData = $this->warehouseService->getQcSummary($startDate, $endDate, $search, $filter);

        return new WarehouseQcSummaryResource($summaryData);
    }
}
