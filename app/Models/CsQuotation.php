<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CsQuotation extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'cs_lead_id',
        'quotation_number',
        'version',
        'items',
        'subtotal',
        'discount',
        'discount_type',
        'total',
        'notes',
        'terms_conditions',
        'status',
        'sent_at',
        'responded_at',
        'rejection_reason',
        'valid_until',
        'shoe_brand',
        'shoe_type',
        'shoe_color',
        'shoe_size',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
        'valid_until' => 'date',
    ];

    // Status Constants
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_SENT = 'SENT';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_REVISED = 'REVISED';

    // Discount Type Constants
    const DISCOUNT_AMOUNT = 'AMOUNT';
    const DISCOUNT_PERCENTAGE = 'PERCENTAGE';

    /**
     * Boot method for auto-generating quotation number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quotation) {
            if (!$quotation->quotation_number) {
                $quotation->quotation_number = self::generateQuotationNumber();
            }
        });
    }

    /**
     * Generate unique quotation number
     */
    public static function generateQuotationNumber()
    {
        $date = date('Ymd');
        $prefix = 'QT-' . $date . '-';
        
        // Find the latest quotation created today to determine max sequence
        $latest = self::withTrashed()
            ->where('quotation_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc') // Change from quotation_number to id for typically faster sort, or stick to quotation_number
            ->orderBy('quotation_number', 'desc')
            ->first();

        if (!$latest) {
             return $prefix . '001';
        }

        // Extract sequence
        $parts = explode('-', $latest->quotation_number);
        $lastSeq = end($parts);
        
        // Ensure it's numeric
        if (!is_numeric($lastSeq)) {
            // Fallback if format is weird
            $count = self::withTrashed()->whereDate('created_at', today())->count() + 1;
            return $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $newSeq = intval($lastSeq) + 1;
        
        return $prefix . str_pad($newSeq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function lead()
    {
        return $this->belongsTo(CsLead::class, 'cs_lead_id');
    }

    /**
     * Get all items in this quotation
     */
    public function quotationItems()
    {
        return $this->hasMany(CsQuotationItem::class, 'quotation_id')->orderBy('item_number');
    }

    /**
     * Scopes
     */
    public function scopeLatestVersion($query)
    {
        return $query->orderBy('version', 'desc');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    /**
     * Helper Methods
     */
    public function calculateTotal()
    {
        $subtotal = collect($this->items)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['qty'] ?? 1);
        });

        $this->subtotal = (float) $subtotal;

        if ($this->discount_type === self::DISCOUNT_PERCENTAGE) {
            $discountAmount = ($subtotal * $this->discount) / 100;
        } else {
            $discountAmount = $this->discount;
        }

        $this->total = $subtotal - $discountAmount;
    }

    public function markAsSent()
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsAccepted()
    {
        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'responded_at' => now(),
        ]);
    }

    public function markAsRejected($reason = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'responded_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function isExpired()
    {
        return $this->valid_until && now()->greaterThan($this->valid_until);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'bg-gray-100 text-gray-700',
            self::STATUS_SENT => 'bg-blue-100 text-blue-700',
            self::STATUS_ACCEPTED => 'bg-green-100 text-green-700',
            self::STATUS_REJECTED => 'bg-red-100 text-red-700',
            self::STATUS_REVISED => 'bg-yellow-100 text-yellow-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
