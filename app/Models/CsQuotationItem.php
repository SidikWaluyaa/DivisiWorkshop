<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CsQuotationItem extends Model
{
    public const SUPPORTED_BRANDS = [
        'Nike', 'Adidas', 'Puma', 'Reebok', 'New Balance', 'Asics', 'Onitsuka', 'Vans', 'Converse', 
        'Eiger', 'Consina', 'Arei', 'Merrell', 'Columbia', 'The North Face', 'Keen', 'La Sportiva', 'Lowa',
        'Jordan', 'Skechers', 'Mizuno', 'Under Armour', 'Fila', 'Saucony', 'Brooks', 
        'Salomon', 'Hoka One One', 'On Running', 'Lacoste', 'Timberland', 'Dr. Martens', 
        'Gucci', 'Balenciaga', 'Louis Vuitton', 'Dior', 'Prada', 'Alexander McQueen', 
        'Saint Laurent', 'Off-White', 'Compass', 'Ventela', 'Specs', 'Eagle', 'Patrobas', 
        'Geoff Max', 'Ortuseight', 'Aerostreet', 'Kodachi', 'Brodo', 'League', 'Piero', 
        'NAH Project', 'Johnson', 'Saint Barkley', 'Wakai',
        // Women's Shoe & Sandal Brands (International & Local)
        'Chanel', 'Hermes', 'Christian Louboutin', 'Jimmy Choo', 'Manolo Blahnik', 
        'Charles & Keith', 'Zara', 'Tory Burch', 'Birkenstock', 'Melissa', 'Crocs', 
        'Steve Madden', 'FitFlop', 'Valentino', 'Yongki Komaladi', 'Fladeo', 'Bata', 
        'Carvil', 'Adorable Projects', 'Winod', 'Blow', 'PVRA', 'Khakikakiku', 'Amazara', 
        'VAIA', 'Pix Footwear', 'Unificati', 'Lainnya'
    ];

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
        'item_notes',
        'services',
        'item_total_price',
        'requested_materials',
        'hk_days',
        'is_warranty',
    ];
    
    protected $casts = [
        'services' => 'array',
        'requested_materials' => 'array',
        'item_total_price' => 'decimal:2',
        'is_warranty' => 'boolean',
        'hk_days' => 'integer',
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

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo_path) {
            return null;
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        return asset('storage/' . $this->photo_path);
    }
}
