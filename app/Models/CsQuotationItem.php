<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CsQuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'item_number',
        'category',
        'shoe_type',
        'shoe_brand',
        'shoe_size',
        'shoe_color',
        'photo_path',
        'condition_notes',
        'services',
        'item_total_price',
    ];

    protected $casts = [
        'services' => 'array',
        'item_total_price' => 'decimal:2',
    ];

    /**
     * Get the quotation that owns this item
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(CsQuotation::class, 'quotation_id');
    }

    /**
     * Get the SPK item linked to this quotation item
     */
    public function spkItem(): HasOne
    {
        return $this->hasOne(CsSpkItem::class, 'quotation_item_id');
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

        return implode(' ', $parts) ?: "Item #{$this->item_number}";
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
}
