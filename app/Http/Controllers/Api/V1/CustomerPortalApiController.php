<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\WorkOrder;
use App\Helpers\PhoneHelper;
use App\Http\Resources\V1\CustomerPortalResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerPortalApiController extends Controller
{
    /**
     * Fetch customer work order history and photos based on phone number.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CustomerPortalResource
     */
    public function getOrdersByPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $phoneInput = $request->get('phone');
        $normalizedPhone = PhoneHelper::normalize($phoneInput);

        if (!$normalizedPhone) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid phone number format.',
            ], 400);
        }

        // Fetch Customer
        $customer = Customer::where('phone', $normalizedPhone)->first();

        // Fetch WorkOrders linked to this phone (safer than relying solely on customer relation)
        $workOrders = WorkOrder::with(['workOrderServices.service', 'photos'])
            ->where('customer_phone', $normalizedPhone)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'customer' => $customer,
            'work_orders' => $workOrders,
            'query_phone' => $normalizedPhone,
        ];

        return new CustomerPortalResource($data);
    }
}
