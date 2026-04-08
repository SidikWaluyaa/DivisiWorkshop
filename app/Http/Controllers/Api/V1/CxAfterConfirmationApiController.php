<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CxAfterConfirmation;
use App\Http\Resources\V1\CxAfterConfirmationResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CxAfterConfirmationApiController extends Controller
{
    /**
     * Get CX After Confirmation sync data for external dashboards (insight.shoeworkshop.com)
     * No pagination limits applied based on business requirement.
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = CxAfterConfirmation::with(['workOrder', 'pic']);

        if ($startDate && $endDate) {
            $query->whereBetween('entered_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        // Return all matching data without limit
        $data = $query->orderBy('entered_at', 'desc')->get();

        return CxAfterConfirmationResource::collection($data);
    }
}
