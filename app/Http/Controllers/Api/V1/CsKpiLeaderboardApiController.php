<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CsDashboardController;
use Illuminate\Http\Request;

class CsKpiLeaderboardApiController extends Controller
{
    /**
     * Get CS KPI Leaderboard performance data as JSON.
     * Delegates to the existing CsDashboardController::getKpiLeaderboardData method.
     * 
     * Query params: ?start_date=2026-06-01&end_date=2026-06-18
     */
    public function index(Request $request)
    {
        $controller = app(CsDashboardController::class);
        return $controller->getKpiLeaderboardData($request);
    }
}
