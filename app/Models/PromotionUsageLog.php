<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionUsageLog extends Model
{
    use HasFactory;

    const CREATED_AT = 'applied_at';
    const UPDATED_AT = null; 

    protected $fillable = [
        'promotion_id',
        'cs_lead_id',
        'cs_spk_id',
        'work_order_id',
        'customer_phone',
        'original_amount',
        'discount_amount',
        'final_amount',
        'applied_by',
        'applied_at',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'applied_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function csLead()
    {
        return $this->belongsTo(CsLead::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function appliedBy()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }
}
