<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingSyncController extends Controller
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
        // Check both query param and header using the same token logic as Work Order Sync
        $token = $request->query('token') ?? $request->header('X-API-TOKEN');
        $validToken = env('WORK_ORDER_SYNC_TOKEN', 'SECRET_TOKEN_12345');

        if ($token !== $validToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // 2. Fetch Data
            $shippings = Shipping::with('workOrder:id,spk_number')
                ->select(
                    'id',
                    'work_order_id',
                    'tanggal_masuk',
                    'customer_name',
                    'customer_phone',
                    'spk_number',
                    'is_verified',
                    'kategori_pengiriman',
                    'tanggal_pengiriman',
                    'pic',
                    'resi_pengiriman'
                )
                ->orderBy('tanggal_masuk', 'desc')
                ->limit(500)
                ->get()
                ->map(function ($shipping) {
                    return [
                        'id' => 'ID-' . $shipping->id,
                        'tanggal_masuk' => $shipping->tanggal_masuk->format('Y-m-d'),
                        'customer_name' => $shipping->customer_name,
                        'customer_phone' => $shipping->customer_phone,
                        'spk_number' => $shipping->spk_number,
                        'is_verified' => $shipping->is_verified ? 'Yes' : 'No',
                        'kategori_pengiriman' => $shipping->kategori_pengiriman,
                        'tanggal_pengiriman' => $shipping->tanggal_pengiriman ? $shipping->tanggal_pengiriman->format('Y-m-d') : null,
                        'pic' => $shipping->pic,
                        'resi_pengiriman' => $shipping->resi_pengiriman,
                    ];
                });

            // 3. Return JSON
            return response()->json([
                'status' => 'success',
                'count' => $shippings->count(),
                'data' => $shippings
            ]);

        } catch (\Exception $e) {
            Log::error('Shipping Sync Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}
