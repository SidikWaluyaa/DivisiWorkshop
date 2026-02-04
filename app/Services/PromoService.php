<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\PromotionUsageLog;
use App\Helpers\PhoneHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PromoService
{
    protected $validationService;

    public function __construct(PromoValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    /**
     * Get all active promos for a specific service
     */
    public function getActivePromosForService(int $serviceId): Collection
    {
        return Promotion::active()
            ->valid()
            ->forService($serviceId)
            ->where(function($query) {
                $query->whereNull('max_usage_total')
                      ->orWhereRaw('current_usage_count < max_usage_total');
            })
            ->orderBy('priority', 'desc')
            ->get();
    }

    /**
     * Get all currently active promos
     */
    public function getAllActivePromos(): Collection
    {
        return Promotion::active()
            ->valid()
            ->orderBy('priority', 'desc')
            ->get();
    }

    /**
     * Find promo by code
     */
    public function findByCode(string $code): ?Promotion
    {
        return Promotion::where('code', strtoupper($code))->first();
    }

    /**
     * Validate promo code with context
     * 
     * @param string $code
     * @param array $context [
     *   'service_ids' => array,
     *   'customer_phone' => string,
     *   'total_amount' => float,
     *   'customer' => Customer|null
     * ]
     * @return array ['valid' => bool, 'message' => string, 'promo' => Promotion|null]
     */
    public function validatePromoCode(string $code, array $context): array
    {
        $promo = $this->findByCode($code);

        if (!$promo) {
            return [
                'valid' => false,
                'message' => 'Kode promo tidak ditemukan',
                'promo' => null,
            ];
        }

        return $this->validationService->validate($promo, $context);
    }

    /**
     * Calculate discount for given promo and amount
     */
    public function calculateDiscount(Promotion $promo, float $totalAmount, array $itemPrices = []): float
    {
        return $promo->calculateDiscount($totalAmount, $itemPrices);
    }

    /**
     * Apply promo to items array
     * 
     * @param array $items - Array of items with services
     * @param Promotion $promo
     * @return array - Modified items with discount applied
     */
    public function applyPromoToItems(array $items, Promotion $promo): array
    {
        $modifiedItems = [];

        foreach ($items as $item) {
            $itemTotal = $item['total'] ?? 0;
            $discount = $this->calculateDiscount($promo, $itemTotal);

            $modifiedItems[] = array_merge($item, [
                'promotion_id' => $promo->id,
                'original_price' => $itemTotal,
                'discount_amount' => $discount,
                'final_price' => $itemTotal - $discount,
            ]);
        }

        return $modifiedItems;
    }

    /**
     * Log promo usage
     * 
     * @param Promotion $promo
     * @param array $data [
     *   'cs_lead_id' => int|null,
     *   'cs_spk_id' => int|null,
     *   'work_order_id' => int|null,
     *   'customer_phone' => string,
     *   'original_amount' => float,
     *   'discount_amount' => float,
     *   'final_amount' => float,
     *   'applied_by' => int
     * ]
     * @return PromotionUsageLog
     */
    public function logPromoUsage(Promotion $promo, array $data): PromotionUsageLog
    {
        return DB::transaction(function () use ($promo, $data) {
            // Lock promotion row for update to prevent race conditions on usage count
            $promo = Promotion::where('id', $promo->id)->lockForUpdate()->first();
            
            // Re-validate limit inside transaction
            if ($promo->max_usage_total !== null && $promo->current_usage_count >= $promo->max_usage_total) {
                throw new \Exception('Maaf, kuota promo ini baru saja habis.');
            }

            // Create log
            $log = PromotionUsageLog::create([
                'promotion_id' => $promo->id,
                'cs_lead_id' => $data['cs_lead_id'] ?? null,
                'cs_spk_id' => $data['cs_spk_id'] ?? null,
                'work_order_id' => $data['work_order_id'] ?? null,
                'customer_phone' => PhoneHelper::normalize($data['customer_phone']),
                'original_amount' => $data['original_amount'],
                'discount_amount' => $data['discount_amount'],
                'final_amount' => $data['final_amount'],
                'applied_by' => $data['applied_by'],
            ]);

            // Increment usage count atomically
            $promo->increment('current_usage_count');

            return $log;
        });
    }

    /**
     * Get customer usage count for a promo
     */
    public function getCustomerUsageCount(Promotion $promo, string $customerPhone): int
    {
        return PromotionUsageLog::where('promotion_id', $promo->id)
            ->where('customer_phone', $customerPhone)
            ->count();
    }

    /**
     * Check if customer has reached their usage limit for a promo
     */
    public function hasCustomerReachedLimit(Promotion $promo, string $customerPhone): bool
    {
        if ($promo->max_usage_per_customer === null) {
            return false;
        }

        $normalizedPhone = PhoneHelper::normalize($customerPhone);

        $usageCount = PromotionUsageLog::where('promotion_id', $promo->id)
            ->where('customer_phone', $normalizedPhone)
            ->count();

        return $usageCount >= $promo->max_usage_per_customer;
    }

    /**
     * Get promo statistics
     */
    public function getPromoStats(Promotion $promo): array
    {
        $logs = $promo->usageLogs;

        return [
            'total_usage' => $logs->count(),
            'total_discount_given' => $logs->sum('discount_amount'),
            'total_revenue_impact' => $logs->sum('final_amount'),
            'unique_customers' => $logs->unique('customer_phone')->count(),
            'average_discount' => $logs->avg('discount_amount'),
        ];
    }

    /**
     * Check if promo can be applied to given services
     */
    public function canApplyToServices(Promotion $promo, array $serviceIds): bool
    {
        if ($promo->applicable_to === Promotion::APPLICABLE_ALL_SERVICES) {
            return true;
        }

        if ($promo->applicable_to === Promotion::APPLICABLE_SPECIFIC_SERVICES) {
            $promoServiceIds = $promo->services->pluck('id')->toArray();
            
            // Check if at least one selected service is in promo services
            foreach ($serviceIds as $serviceId) {
                if (in_array($serviceId, $promoServiceIds)) {
                    return true;
                }
            }
            
            return false;
        }

        return false;
    }

    /**
     * Get best promo for given context
     * Returns the promo with highest discount
     */
    public function getBestPromo(array $serviceIds, float $totalAmount, ?string $customerPhone = null): ?Promotion
    {
        $activePromos = $this->getAllActivePromos();
        $bestPromo = null;
        $maxDiscount = -1;

        foreach ($activePromos as $promo) {
            // Check basic constraints
            if (!$this->canApplyToServices($promo, $serviceIds)) {
                continue;
            }

            // Check validation
            $validation = $this->validatePromoCode($promo->code, [
                'service_ids' => $serviceIds,
                'customer_phone' => $customerPhone,
                'total_amount' => $totalAmount
            ]);

            if ($validation['valid']) {
                $discount = $this->calculateDiscount($promo, $totalAmount);
                
                if ($discount > $maxDiscount) {
                    $maxDiscount = $discount;
                    $bestPromo = $promo;
                }
            }
        }

        return $bestPromo;
    }
}
