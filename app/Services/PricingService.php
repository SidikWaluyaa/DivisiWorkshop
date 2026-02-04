<?php

namespace App\Services;

use App\Models\Promotion;

class PricingService
{
    protected $promoService;

    public function __construct(PromoService $promoService)
    {
        $this->promoService = $promoService;
    }

    /**
     * Calculate item price with services and optional promo
     * 
     * @param array $services - Array of service objects with 'price' field
     * @param Promotion|null $promo
     * @return array [
     *   'subtotal' => float,
     *   'discount' => float,
     *   'total' => float,
     *   'promotion_id' => int|null
     * ]
     */
    public function calculateItemPrice(array $services, ?Promotion $promo = null): array
    {
        // Calculate subtotal from services
        $subtotal = 0;
        foreach ($services as $service) {
            $subtotal += $service['price'] ?? $service->price ?? 0;
        }

        // Calculate discount if promo exists
        $discount = 0;
        $promotionId = null;

        if ($promo) {
            $discount = $this->promoService->calculateDiscount($promo, $subtotal);
            $promotionId = $promo->id;
        }

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'total' => round($subtotal - $discount, 2),
            'promotion_id' => $promotionId,
        ];
    }

    /**
     * Calculate total for all items with promos
     * 
     * @param array $items - Array of items, each with 'services' and optional 'promo'
     * @return array [
     *   'items' => array,
     *   'grand_subtotal' => float,
     *   'grand_discount' => float,
     *   'grand_total' => float
     * ]
     */
    public function calculateTotalWithPromos(array $items): array
    {
        $processedItems = [];
        $grandSubtotal = 0;
        $grandDiscount = 0;

        foreach ($items as $item) {
            $services = $item['services'] ?? [];
            $promo = $item['promo'] ?? null;

            $pricing = $this->calculateItemPrice($services, $promo);

            $processedItems[] = array_merge($item, [
                'subtotal' => $pricing['subtotal'],
                'discount' => $pricing['discount'],
                'total' => $pricing['total'],
                'promotion_id' => $pricing['promotion_id'],
            ]);

            $grandSubtotal += $pricing['subtotal'];
            $grandDiscount += $pricing['discount'];
        }

        return [
            'items' => $processedItems,
            'grand_subtotal' => round($grandSubtotal, 2),
            'grand_discount' => round($grandDiscount, 2),
            'grand_total' => round($grandSubtotal - $grandDiscount, 2),
        ];
    }

    /**
     * Get discount breakdown for display
     * 
     * @param array $items - Processed items with pricing info
     * @return array [
     *   ['promo_code' => string, 'promo_name' => string, 'discount' => float],
     *   ...
     * ]
     */
    public function getDiscountBreakdown(array $items): array
    {
        $breakdown = [];
        $promoDiscounts = [];

        foreach ($items as $item) {
            if (isset($item['promotion_id']) && $item['discount'] > 0) {
                $promoId = $item['promotion_id'];
                
                if (!isset($promoDiscounts[$promoId])) {
                    $promo = Promotion::find($promoId);
                    $promoDiscounts[$promoId] = [
                        'promo_code' => $promo->code,
                        'promo_name' => $promo->name,
                        'discount' => 0,
                    ];
                }

                $promoDiscounts[$promoId]['discount'] += $item['discount'];
            }
        }

        foreach ($promoDiscounts as $data) {
            $breakdown[] = [
                'promo_code' => $data['promo_code'],
                'promo_name' => $data['promo_name'],
                'discount' => round($data['discount'], 2),
            ];
        }

        return $breakdown;
    }

    /**
     * Apply max discount cap if exists
     */
    public function applyMaxDiscountCap(float $discount, ?float $maxDiscount): float
    {
        if ($maxDiscount === null) {
            return $discount;
        }

        return min($discount, $maxDiscount);
    }

    /**
     * Calculate service price with promo
     * 
     * @param float $originalPrice
     * @param Promotion|null $promo
     * @return array ['original' => float, 'discount' => float, 'final' => float]
     */
    public function calculateServicePrice(float $originalPrice, ?Promotion $promo = null): array
    {
        $discount = 0;

        if ($promo) {
            $discount = $this->promoService->calculateDiscount($promo, $originalPrice);
        }

        return [
            'original' => round($originalPrice, 2),
            'discount' => round($discount, 2),
            'final' => round($originalPrice - $discount, 2),
        ];
    }

    /**
     * Format price for display
     */
    public function formatPrice(float $price): string
    {
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    /**
     * Calculate percentage saved
     */
    public function calculatePercentageSaved(float $original, float $discount): float
    {
        if ($original <= 0) {
            return 0;
        }

        return round(($discount / $original) * 100, 2);
    }
}
