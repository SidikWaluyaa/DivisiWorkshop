<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkOrderSyncController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 1. Security Check (Simple Token)
        // Check both query param and header
        $token = $request->query('token') ?? $request->header('X-API-TOKEN');
        $validToken = env('WORK_ORDER_SYNC_TOKEN', 'SECRET_TOKEN_12345');

        if ($token !== $validToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // 2. Fetch Data
            // Using Eloquent or Query Builder
            $workOrders = WorkOrder::select(
                'id',
                'spk_number as ticket_number',
                'customer_name',
                'customer_phone',
                'shoe_brand as brand',
                'shoe_type as type',
                'category',
                'status_pembayaran as payment_status',
                'status as order_status',
                'total_transaksi as total_price',
                'created_at',
                'estimation_date'
            )
            ->orderBy('created_at', 'desc')
            ->limit(500)
            ->get();

            // 3. Return JSON
            return response()->json([
                'status' => 'success',
                'count' => $workOrders->count(),
                'data' => $workOrders
            ]);

        } catch (\Exception $e) {
            Log::error('Work Order Sync Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}
