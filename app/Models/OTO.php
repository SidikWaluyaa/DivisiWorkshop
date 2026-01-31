<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OTO extends Model
{
    use SoftDeletes;
    protected $table = 'otos';
    
    protected $fillable = [
        'work_order_id',
        'title',
        'description',
        'oto_type',
        'proposed_services',
        'total_normal_price',
        'total_oto_price',
        'total_discount',
        'discount_percent',
        'estimated_days',
        'valid_until',
        'status',
        'customer_responded_at',
        'customer_note',
        'started_at',
        'completed_at',
        'is_fast_track',
        'dp_required',
        'dp_paid',
        'dp_paid_at',
        'materials_reserved',
        'materials_confirmed',
        'created_by',
        'cx_assigned_to',
        'cx_contacted_at',
        'cx_contact_method',
        'cx_notes',
        'cx_follow_up_count',
    ];

    protected $casts = [
        'proposed_services' => 'array',
        'total_normal_price' => 'decimal:2',
        'total_oto_price' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'dp_required' => 'decimal:2',
        'valid_until' => 'datetime',
        'customer_responded_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'dp_paid_at' => 'datetime',
        'cx_contacted_at' => 'datetime',
        'is_fast_track' => 'boolean',
        'dp_paid' => 'boolean',
        'materials_reserved' => 'boolean',
        'materials_confirmed' => 'boolean',
    ];

    /**
     * Get the work order that owns the OTO
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the user who created the OTO
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the material reservations for this OTO
     */
    public function materialReservations(): HasMany
    {
        return $this->hasMany(MaterialReservation::class, 'oto_id');
    }

    /**
     * Get the CX user assigned to this OTO
     */
    public function cxAssigned(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cx_assigned_to');
    }

    /**
     * Get the contact logs for this OTO
     */
    public function contactLogs(): HasMany
    {
        return $this->hasMany(OTOContactLog::class, 'oto_id');
    }

    /**
     * Check if OTO is expired
     */
    public function isExpired(): bool
    {
        return $this->valid_until < now() && $this->status === 'PENDING_CUSTOMER';
    }

    /**
     * Check if OTO is pending customer response
     */
    public function isPending(): bool
    {
        return $this->status === 'PENDING_CUSTOMER' && !$this->isExpired();
    }

    /**
     * Check if OTO was accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === 'ACCEPTED';
    }

    /**
     * Calculate DP amount (50% of OTO price)
     */
    public function calculateDP(): float
    {
        return $this->total_oto_price * 0.5;
    }

    /**
     * Scope for active OTOs
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['PENDING_CUSTOMER', 'ACCEPTED', 'IN_PROGRESS']);
    }

    /**
     * Scope for pending customer response
     */
    public function scopePendingCustomer($query)
    {
        return $query->where('status', 'PENDING_CUSTOMER')
                     ->where('valid_until', '>', now());
    }

    /**
     * Scope for expired OTOs
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'PENDING_CUSTOMER')
                     ->where('valid_until', '<=', now());
    }
}
