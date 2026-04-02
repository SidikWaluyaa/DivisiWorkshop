<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\FinanceApiService;
use App\Http\Resources\V1\FinanceSyncResource;
use Illuminate\Http\Request;

class FinanceSyncController extends Controller
{
    protected $financeService;

    public function __construct(FinanceApiService $financeService)
    {
        $this->financeService = $financeService;
    }

    /**
     * Get finance sync data
     * 
     * @param Request $request
     * @return FinanceSyncResource
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $financeData = $this->financeService->getFinanceSyncData($startDate, $endDate);

        return FinanceSyncResource::collection($financeData);
    }
}
