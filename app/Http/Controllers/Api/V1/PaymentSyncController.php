<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PaymentApiService;
use App\Http\Resources\V1\PaymentSyncResource;
use Illuminate\Http\Request;

class PaymentSyncController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentApiService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Get payment sync data for insight domain
     * 
     * @param Request $request
     * @return PaymentSyncResource
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $payments = $this->paymentService->getPaymentSyncData($startDate, $endDate);

        return PaymentSyncResource::collection($payments);
    }
}
