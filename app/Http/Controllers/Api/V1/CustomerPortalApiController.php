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

    /**
     * Fetch list of all customers, optionally filtered by search query.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomers(Request $request)
    {
        $search = $request->get('search');
        $perPage = min((int) $request->get('per_page', 50), 100);

        $query = Customer::query()->withCount('workOrders');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name', 'asc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => collect($customers->items())->map(function($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'address' => $customer->address,
                    'city' => $customer->city,
                    'province' => $customer->province,
                    'district' => $customer->district,
                    'village' => $customer->village,
                    'postal_code' => $customer->postal_code,
                    'total_orders' => $customer->work_orders_count,
                    'created_at' => $customer->created_at ? $customer->created_at->toDateTimeString() : null,
                ];
            }),
            'links' => [
                'first' => $customers->url(1),
                'last' => $customers->url($customers->lastPage()),
                'prev' => $customers->previousPageUrl(),
                'next' => $customers->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $customers->currentPage(),
                'from' => $customers->firstItem(),
                'last_page' => $customers->lastPage(),
                'path' => $customers->path(),
                'per_page' => $customers->perPage(),
                'to' => $customers->lastItem(),
                'total' => $customers->total(),
            ],
            'message' => 'Customers retrieved successfully.'
        ]);
    }
}
