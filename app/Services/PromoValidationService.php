<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\Customer;
use App\Helpers\PhoneHelper;
use Carbon\Carbon;

class PromoValidationService
{
    protected $promoService;

    public function __construct()
    {
        // Avoid circular dependency by not injecting PromoService
    }

    /**
     * Validate all promo rules
     * 
     * @param Promotion $promo
     * @param array $context [
     *   'service_ids' => array,
     *   'customer_phone' => string|null,
     *   'total_amount' => float,
     *   'customer' => Customer|null
     * ]
     * @return array ['valid' => bool, 'message' => string, 'promo' => Promotion]
     */
    public function validate(Promotion $promo, array $context): array
    {
        // 1. Check if promo is active
        if (!$this->isPromoActive($promo)) {
            return $this->failResponse('Promo tidak aktif', $promo);
        }

        // 2. Check validity period
        if (!$this->isInValidPeriod($promo)) {
            return $this->failResponse('Promo sudah tidak berlaku atau belum dimulai', $promo);
        }

        // 3. Check total usage limit
        if ($this->hasReachedUsageLimit($promo)) {
            return $this->failResponse('Promo sudah mencapai batas penggunaan', $promo);
        }

        // 4. Check customer usage limit
        if (isset($context['customer_phone']) && $this->hasCustomerReachedLimit($promo, $context['customer_phone'])) {
            return $this->failResponse('Anda sudah mencapai batas penggunaan promo ini', $promo);
        }

        // 5. Check customer tier eligibility
        $customer = $context['customer'] ?? null;
        if (!$this->isCustomerEligible($promo, $customer)) {
            return $this->failResponse('Promo ini hanya untuk customer ' . $promo->customer_tier, $promo);
        }

        // 6. Check minimum purchase
        $amount = $context['total_amount'] ?? 0;
        if (!$this->meetsMinimumPurchase($promo, $amount)) {
            $minAmount = number_format((float) $promo->min_purchase_amount, 0, ',', '.');
            return $this->failResponse("Minimum pembelian Rp {$minAmount} untuk promo ini", $promo);
        }

        // 7. Check service applicability
        $serviceIds = $context['service_ids'] ?? [];
        if (!$this->isApplicableToServices($promo, $serviceIds)) {
            return $this->failResponse('Promo tidak berlaku untuk layanan yang dipilih', $promo);
        }

        // 8. Check bundle requirements
        if (!$this->meetsBundleRequirements($promo, $serviceIds)) {
            return $this->failResponse('Promo bundle memerlukan semua layanan yang ditentukan', $promo);
        }

        // All validations passed
        return $this->successResponse($promo);
    }

    /**
     * Check if promo is currently active
     */
    public function isPromoActive(Promotion $promo): bool
    {
        return $promo->is_active === true;
    }

    /**
     * Check if current date is within validity period
     */
    public function isInValidPeriod(Promotion $promo): bool
    {
        $now = Carbon::now();
        return $now->gte($promo->valid_from) && $now->lte($promo->valid_until);
    }

    /**
     * Check if promo has reached total usage limit
     */
    public function hasReachedUsageLimit(Promotion $promo): bool
    {
        if ($promo->max_usage_total === null) {
            return false;
        }

        return $promo->current_usage_count >= $promo->max_usage_total;
    }

    /**
     * Check if customer has reached personal usage limit
     */
    public function hasCustomerReachedLimit(Promotion $promo, string $customerPhone): bool
    {
        if ($promo->max_usage_per_customer === null) {
            return false;
        }

        $normalizedPhone = PhoneHelper::normalize($customerPhone);

        $usageCount = \App\Models\PromotionUsageLog::where('promotion_id', $promo->id)
            ->where('customer_phone', $normalizedPhone)
            ->count();

        return $usageCount >= $promo->max_usage_per_customer;
    }

    /**
     * Check if customer is eligible based on tier
     */
    public function isCustomerEligible(Promotion $promo, ?Customer $customer): bool
    {
        // If promo is for all customers
        if ($promo->customer_tier === Promotion::TIER_ALL) {
            return true;
        }

        // If customer is not provided, assume not eligible for tier-specific promos
        if (!$customer) {
            return $promo->customer_tier === Promotion::TIER_ALL;
        }

        // Check customer tier
        $customerTier = $customer->tier ?? 'REGULAR';
        
        return $promo->customer_tier === $customerTier;
    }

    /**
     * Check if total amount meets minimum purchase requirement
     */
    public function meetsMinimumPurchase(Promotion $promo, float $amount): bool
    {
        if ($promo->min_purchase_amount === null) {
            return true;
        }

        return $amount >= $promo->min_purchase_amount;
    }

    /**
     * Check if promo is applicable to selected services
     */
    public function isApplicableToServices(Promotion $promo, array $serviceIds): bool
    {
        // If applicable to all services
        if ($promo->applicable_to === Promotion::APPLICABLE_ALL_SERVICES) {
            return true;
        }

        // If applicable to specific services
        if ($promo->applicable_to === Promotion::APPLICABLE_SPECIFIC_SERVICES) {
            if (empty($serviceIds)) {
                return false;
            }

            $promoServiceIds = $promo->services->pluck('id')->toArray();
            
            // Check if at least one selected service is in promo services
            foreach ($serviceIds as $serviceId) {
                if (in_array($serviceId, $promoServiceIds)) {
                    return true;
                }
            }
            
            return false;
        }

        // For categories, would need category info
        // For now, return true
        return true;
    }

    /**
     * Check if bundle requirements are met
     */
    public function meetsBundleRequirements(Promotion $promo, array $serviceIds): bool
    {
        // Only check for bundle type promos
        if ($promo->type !== Promotion::TYPE_BUNDLE) {
            return true;
        }

        // Get bundle requirements
        $bundles = $promo->bundles;
        
        if ($bundles->isEmpty()) {
            return true;
        }

        // Check if any bundle requirement is met
        foreach ($bundles as $bundle) {
            if ($bundle->isApplicable($serviceIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Helper: Create success response
     */
    protected function successResponse(Promotion $promo): array
    {
        return [
            'valid' => true,
            'message' => 'Promo valid',
            'promo' => $promo,
        ];
    }

    /**
     * Helper: Create fail response
     */
    protected function failResponse(string $message, Promotion $promo): array
    {
        return [
            'valid' => false,
            'message' => $message,
            'promo' => $promo,
        ];
    }
}
