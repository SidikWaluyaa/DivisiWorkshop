<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CsSpk extends Model
{
    use SoftDeletes;
    protected $table = 'cs_spk';

    protected $fillable = [
        'cs_lead_id',
        'work_order_id',
        'spk_number',
        'customer_id',
        'services',
        'total_price',
        'dp_amount',
        'dp_status',
        'dp_paid_at',
        'payment_method',
        'payment_notes',
        'expected_delivery_date',
        'special_instructions',
        'shoe_brand',
        'shoe_type',
        'shoe_color',
        'shoe_size',
        'category',
        'priority',
        'delivery_type',
        'cs_code',
        'proof_image',
        'pdf_path',
        'status',
        'handed_at',
        'handed_by',
    ];

    protected $casts = [
        'services' => 'array',
        'total_price' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'dp_paid_at' => 'datetime',
        'expected_delivery_date' => 'date',
        'handed_at' => 'datetime',
    ];

    // Status Constants
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_WAITING_DP = 'WAITING_DP';
    const STATUS_WAITING_VERIFICATION = 'WAITING_VERIFICATION';
    const STATUS_DP_PAID = 'DP_PAID';
    const STATUS_HANDED_TO_WORKSHOP = 'HANDED_TO_WORKSHOP';

    // DP Status Constants
    const DP_PENDING = 'PENDING';
    const DP_PAID = 'PAID';
    const DP_WAIVED = 'WAIVED';

    /**
     * Boot method for auto-generating SPK number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($spk) {
            if (!$spk->spk_number) {
                $spk->spk_number = self::generateSpkNumber();
            }
        });

        static::deleting(function ($spk) {
            $spk->items()->delete();
        });
    }

    /**
     * Generate unique SPK number based on formula:
     * [ShippingCode]-[YearMonth]-[Date]-[Sequence]-[CSCode]
     * Example: F-2505-31-9864-QA
     */
    public static function generateSpkNumber($deliveryType = 'Offline', $csCode = 'SW')
    {
        $shippingCodes = [
            'Offline' => 'F',
            'Online' => 'N',
            'Pickup' => 'P',
            'Ojol' => 'O',
        ];

        $code = $shippingCodes[$deliveryType] ?? substr($deliveryType, 0, 1) ?? 'F';
        $yearMonth = date('y') . date('m');
        $day = date('d');

        // Find current max sequence from both WorkOrder and CsSpk
        $maxSequence = 0;
        
        // We look at recent records to find the highest sequence number
        $workOrders = \App\Models\WorkOrder::withTrashed()->where('spk_number', 'like', '%-%-%-%-%')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();
            
        foreach ($workOrders as $wo) {
            $parts = explode('-', $wo->spk_number);
            if (count($parts) >= 4 && is_numeric($parts[3])) {
                $maxSequence = max($maxSequence, (int)$parts[3]);
            }
        }
        
        $csSpks = \App\Models\CsSpk::withTrashed()->where('spk_number', 'like', '%-%-%-%-%')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();
            
        foreach ($csSpks as $spk) {
            $parts = explode('-', $spk->spk_number);
            if (count($parts) >= 4 && is_numeric($parts[3])) {
                $maxSequence = max($maxSequence, (int)$parts[3]);
            }
        }

        $nextSequence = str_pad($maxSequence + 1, 4, '0', STR_PAD_LEFT);
        
        // Ensure CS Code is sanitized
        $csCode = strtoupper($csCode ?: 'SW');

        return "{$code}-{$yearMonth}-{$day}-{$nextSequence}-{$csCode}";
    }

    /**
     * Relationships
     */
    public function lead()
    {
        return $this->belongsTo(CsLead::class, 'cs_lead_id');
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all items in this SPK
     */
    public function items()
    {
        return $this->hasMany(CsSpkItem::class, 'spk_id');
    }

    public function handedByUser()
    {
        return $this->belongsTo(User::class, 'handed_by');
    }

    /**
     * Scopes
     */
    public function scopeWaitingDp($query)
    {
        return $query->where('dp_status', self::DP_PENDING);
    }

    public function scopeDpPaid($query)
    {
        return $query->where('dp_status', self::DP_PAID);
    }

    public function scopeReadyToHand($query)
    {
        return $query->where('status', self::STATUS_DP_PAID)
                     ->whereNull('work_order_id');
    }

    /**
     * Helper Methods
     */
    public function markDpAsPaid($paymentMethod = null, $notes = null, $proofImage = null)
    {
        $this->update([
            'dp_status' => self::DP_PAID,
            'dp_paid_at' => now(),
            'payment_method' => $paymentMethod,
            'payment_notes' => $notes,
            'proof_image' => $proofImage,
            'status' => self::STATUS_DP_PAID,
        ]);
    }

    public function submitForVerification($paymentMethod = null, $notes = null, $proofImage = null)
    {
        $this->update([
            'payment_method' => $paymentMethod,
            'payment_notes' => $notes,
            'proof_image' => $proofImage,
            'status' => self::STATUS_WAITING_VERIFICATION,
        ]);
    }

    public function handToWorkshop($workOrderId, $userId)
    {
        $this->update([
            'work_order_id' => $workOrderId,
            'status' => self::STATUS_HANDED_TO_WORKSHOP,
            'handed_at' => now(),
            'handed_by' => $userId,
        ]);
    }

    public function getRemainingPaymentAttribute()
    {
        return $this->total_price - $this->dp_amount;
    }

    public function getDpPercentageAttribute()
    {
        if ($this->total_price == 0) return 0;
        return round(($this->dp_amount / $this->total_price) * 100, 2);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'bg-gray-100 text-gray-700',
            self::STATUS_WAITING_DP => 'bg-yellow-100 text-yellow-700',
            self::STATUS_WAITING_VERIFICATION => 'bg-orange-500 text-white shadow-sm font-black animate-pulse',
            self::STATUS_DP_PAID => 'bg-green-100 text-green-700',
            self::STATUS_HANDED_TO_WORKSHOP => 'bg-blue-100 text-blue-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_WAITING_DP => 'Menunggu DP',
            self::STATUS_WAITING_VERIFICATION => 'Verifikasi Finance',
            self::STATUS_DP_PAID => 'DP Lunas',
            self::STATUS_HANDED_TO_WORKSHOP => 'Workshop',
            default => $this->status,
        };
    }

    public function getDpStatusBadgeClassAttribute()
    {
        return match($this->dp_status) {
            self::DP_PENDING => 'bg-yellow-100 text-yellow-700',
            self::DP_PAID => 'bg-green-100 text-green-700',
            self::DP_WAIVED => 'bg-gray-100 text-gray-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function canBeHandedToWorkshop()
    {
        return ($this->status === self::STATUS_DP_PAID || $this->status === self::STATUS_WAITING_DP) &&
               !$this->work_order_id;
    }
}
