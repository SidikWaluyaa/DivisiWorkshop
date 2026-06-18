<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CsDashboardController;
use Illuminate\Http\Request;

class CsForecastingApiController extends Controller
{
    /**
     * Get CS forecasting monthly comparison data as JSON.
     * Delegates to the existing CsDashboardController::getForecastingData method.
     * 
     * Query params: ?year=2026
     */
    public function index(Request $request)
    {
        $controller = app(CsDashboardController::class);
        return $controller->getForecastingData($request);
    }
}
