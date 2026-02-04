<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsSpkItem extends Model
{
    protected $fillable = [
        'spk_id',
        'quotation_item_id',
        'work_order_id',
        'category',
        'shoe_type',
        'shoe_brand',
        'shoe_size',
        'shoe_color',
        'services',
        'item_total_price',
        'item_notes',
        'promotion_id',
        'original_price',
        'discount_amount',
        'status',
    ];

    protected $casts = [
        'services' => 'array',
        'item_total_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    // Status constants
    const STATUS_PENDING = 'PENDING';
    const STATUS_HANDED_TO_WORKSHOP = 'HANDED_TO_WORKSHOP';
    const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    const STATUS_COMPLETED = 'COMPLETED';

    /**
     * Get the SPK that owns this item
     */
    public function spk(): BelongsTo
    {
        return $this->belongsTo(CsSpk::class, 'spk_id');
    }

    /**
     * Get the quotation item this is linked to
     */
    public function quotationItem(): BelongsTo
    {
        return $this->belongsTo(CsQuotationItem::class, 'quotation_item_id');
    }

    /**
     * Get the work order created for this item
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    /**
     * Get the promotion applied to this item
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }

    /**
     * Get final price after discount
     */
    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->item_total_price ?? 0);
    }

    /**
     * Check if item has promo applied
     */
    public function hasPromo(): bool
    {
        return $this->promotion_id !== null && $this->discount_amount > 0;
    }

    /**
     * Get formatted item label
     */
    public function getLabelAttribute(): string
    {
        $parts = array_filter([
            $this->shoe_brand,
            $this->shoe_type,
            $this->shoe_size ? "Size {$this->shoe_size}" : null,
        ]);

        return implode(' ', $parts) ?: "Item";
    }

    /**
     * Get category prefix for SPK generation
     */
    public function getCategoryPrefixAttribute(): string
    {
        return match($this->category) {
            'Sepatu' => 'Sepatu',
            'Tas' => 'Tas',
            'Dompet' => 'Tas',
            'Topi' => 'Headwear',
            'Helm' => 'Headwear',
            'Jaket', 'Baju' => 'Apparel',
            default => 'Lainnya',
        };
    }

    /**
     * Get category icon
     */
    public function getCategoryIconAttribute(): string
    {
        return match($this->category) {
            'Sepatu' => '👟',
            'Tas' => '👜',
            'Dompet' => '👛',
            'Topi' => '🧢',
            default => '📦',
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_HANDED_TO_WORKSHOP => 'bg-blue-100 text-blue-800',
            self::STATUS_IN_PROGRESS => 'bg-purple-100 text-purple-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
