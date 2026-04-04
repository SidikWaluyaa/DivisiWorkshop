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

        // Calculate consolidated summary for external dashboards
        $totalNominal = ($data['upsell']['total_nominal'] ?? 0) + ($data['upsell']['oto_nominal'] ?? 0);
        $totalVolume = ($data['upsell']['total_volume'] ?? 0) + ($data['upsell']['oto_volume'] ?? 0);
        $combinedArpu = $totalVolume > 0 ? $totalNominal / $totalVolume : 0;

        return response()->json([
            'status' => 'success',
            'summary' => [
                'total_nominal' => (float)$totalNominal,
                'total_volume' => (int)$totalVolume,
                'combined_arpu' => (float)$combinedArpu,
                'currency' => 'IDR'
            ],
            'data' => $data
        ]);
    }
}
