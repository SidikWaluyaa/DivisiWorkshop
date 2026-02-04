<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'discount_percentage',
        'discount_amount',
        'max_discount_amount',
        'min_purchase_amount',
        'valid_from',
        'valid_until',
        'is_active',
        'applicable_to',
        'customer_tier',
        'max_usage_total',
        'max_usage_per_customer',
        'current_usage_count',
        'is_stackable',
        'priority',
        'created_by',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'is_stackable' => 'boolean',
        'current_usage_count' => 'integer',
        'max_usage_total' => 'integer',
        'max_usage_per_customer' => 'integer',
        'priority' => 'integer',
    ];

    // Promo Types
    const TYPE_PERCENTAGE = 'PERCENTAGE';
    const TYPE_FIXED = 'FIXED';
    const TYPE_BUNDLE = 'BUNDLE';
    const TYPE_BOGO = 'BOGO';

    // Applicability
    const APPLICABLE_ALL_SERVICES = 'ALL_SERVICES';
    const APPLICABLE_SPECIFIC_SERVICES = 'SPECIFIC_SERVICES';
    const APPLICABLE_CATEGORIES = 'CATEGORIES';

    // Customer Tiers
    const TIER_ALL = 'ALL';
    const TIER_VIP = 'VIP';
    const TIER_REGULAR = 'REGULAR';
    const TIER_NEW = 'NEW';

    /**
     * Relationships
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'promotion_services');
    }

    public function bundles()
    {
        return $this->hasMany(PromotionBundle::class);
    }

    public function usageLogs()
    {
        return $this->hasMany(PromotionUsageLog::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('valid_from', '<=', $now)
                     ->where('valid_until', '>=', $now);
    }

    public function scopeForService($query, int $serviceId)
    {
        return $query->where(function($q) use ($serviceId) {
            $q->where('applicable_to', self::APPLICABLE_ALL_SERVICES)
              ->orWhereHas('services', function($sq) use ($serviceId) {
                  $sq->where('services.id', $serviceId);
              });
        });
    }

    /**
     * Check if promo is currently valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        if ($now->lt($this->valid_from) || $now->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    /**
     * Check if promo has reached usage limit
     */
    public function hasReachedLimit(): bool
    {
        if ($this->max_usage_total === null) {
            return false;
        }

        return $this->current_usage_count >= $this->max_usage_total;
    }

    /**
     * Calculate discount amount for given price
     */
    public function calculateDiscount(float $totalAmount, array $itemPrices = []): float
    {
        $discount = 0;

        switch ($this->type) {
            case self::TYPE_PERCENTAGE:
                $discount = $totalAmount * ($this->discount_percentage / 100);
                break;

            case self::TYPE_FIXED:
                // Fixed discount is applied once to the total
                $discount = $this->discount_amount;
                break;

            case self::TYPE_BUNDLE:
                if ($this->discount_percentage) {
                    $discount = $totalAmount * ($this->discount_percentage / 100);
                } elseif ($this->discount_amount) {
                    $discount = $this->discount_amount;
                }
                break;

            case self::TYPE_BOGO:
                // Buy One Get One (Free item is the cheapest one)
                if (count($itemPrices) >= 2) {
                    sort($itemPrices);
                    // Free item is the first one (cheapest)
                    $discount = $itemPrices[0];
                }
                break;
        }

        // Apply max discount cap if exists
        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        // Discount cannot exceed total amount
        if ($discount > $totalAmount) {
            $discount = $totalAmount;
        }

        return round((float) $discount, 2);
    }

    /**
     * Get badge text for UI display
     */
    public function getBadgeTextAttribute(): string
    {
        switch ($this->type) {
            case self::TYPE_PERCENTAGE:
                return "🎉 PROMO {$this->discount_percentage}%";

            case self::TYPE_FIXED:
                return "🎉 DISKON Rp " . number_format((float) $this->discount_amount, 0, ',', '.');

            case self::TYPE_BUNDLE:
                return "🎁 BUNDLE PROMO";

            case self::TYPE_BOGO:
                return "🎁 BOGO";

            default:
                return "🎉 PROMO";
        }
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('current_usage_count');
    }
}
