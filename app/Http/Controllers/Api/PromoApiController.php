<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PromoService;
use App\Services\PromoValidationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PromoApiController extends Controller
{
    protected $promoService;
    protected $validationService;

    public function __construct(PromoService $promoService, PromoValidationService $validationService)
    {
        $this->promoService = $promoService;
        $this->validationService = $validationService;
    }

    /**
     * Get active promos for a specific service
     * 
     * GET /api/cs/services/{serviceId}/promos
     */
    public function getPromosForService(int $serviceId): JsonResponse
    {
        try {
            $promos = $this->promoService->getActivePromosForService($serviceId);

            return response()->json([
                'success' => true,
                'data' => $promos->map(function ($promo) {
                    return [
                        'id' => $promo->id,
                        'code' => $promo->code,
                        'name' => $promo->name,
                        'description' => $promo->description,
                        'type' => $promo->type,
                        'badge_text' => $promo->badge_text,
                        'discount_percentage' => $promo->discount_percentage,
                        'discount_amount' => $promo->discount_amount,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data promo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate promo code
     * 
     * POST /api/cs/promos/validate
     * Body: {
     *   code: string,
     *   service_ids: array,
     *   customer_phone: string,
     *   total_amount: float
     * }
     */
    public function validatePromoCode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'service_ids' => 'nullable|array',
            'total_amount' => 'required|numeric|min:0',
            'customer_phone' => 'nullable|string',
        ]);

        try {
            $result = $this->promoService->validatePromoCode($request->code, [
                'service_ids' => $request->service_ids,
                'customer_phone' => $request->customer_phone,
                'total_amount' => $request->total_amount,
            ]);

            if ($result['valid']) {
                $promo = $result['promo'];
                $discount = $this->promoService->calculateDiscount($promo, $request->total_amount);

                return response()->json([
                    'success' => true,
                    'valid' => true,
                    'message' => $result['message'],
                    'promo' => [
                        'id' => $promo->id,
                        'code' => $promo->code,
                        'name' => $promo->name,
                        'type' => $promo->type,
                        'badge_text' => $promo->badge_text,
                    ],
                    'discount' => $discount,
                    'final_amount' => $request->total_amount - $discount,
                ]);
            }

            return response()->json([
                'success' => true,
                'valid' => false,
                'message' => $result['message'],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memvalidasi kode promo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate discount for given promo and amount
     * 
     * POST /api/cs/promos/calculate-discount
     * Body: {
     *   promo_id: int,
     *   amount: float
     * }
     */
    public function calculateDiscount(Request $request): JsonResponse
    {
        $request->validate([
            'promo_id' => 'required|exists:promotions,id',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            $promo = \App\Models\Promotion::findOrFail($request->promo_id);
            $discount = $this->promoService->calculateDiscount($promo, $request->amount);

            return response()->json([
                'success' => true,
                'discount' => $discount,
                'final_amount' => $request->amount - $discount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung diskon',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all active promos
     * 
     * GET /api/cs/promos/active
     */
    public function getActivePromos(): JsonResponse
    {
        try {
            $promos = $this->promoService->getAllActivePromos();

            return response()->json([
                'success' => true,
                'data' => $promos->map(function ($promo) {
                    return [
                        'id' => $promo->id,
                        'code' => $promo->code,
                        'name' => $promo->name,
                        'description' => $promo->description,
                        'type' => $promo->type,
                        'badge_text' => $promo->badge_text,
                        'valid_from' => $promo->valid_from->format('Y-m-d'),
                        'valid_until' => $promo->valid_until->format('Y-m-d'),
                        'applicable_to' => $promo->applicable_to,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data promo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
