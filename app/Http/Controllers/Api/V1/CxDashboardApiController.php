<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CxDashboardService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CxDashboardApiController extends Controller
{
    protected $cxService;

    public function __construct(CxDashboardService $cxService)
    {
        $this->cxService = $cxService;
    }

    /**
     * Get CX Performance Summary
     */
    public function index(Request $request)
    {
        // Default using today's range
        $start = $request->has('start') ? Carbon::parse($request->get('start')) : Carbon::now()->startOfDay();
        $end = $request->has('end') ? Carbon::parse($request->get('end')) : Carbon::now()->endOfDay();
        
        $forceRefresh = $request->boolean('refresh', false);

        $data = $this->cxService->getSummary($start, $end, $forceRefresh);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
