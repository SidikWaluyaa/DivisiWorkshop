<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_date',
        'notes',
        'verified',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'verified' => 'boolean',
    ];

    /**
     * Get the invoice this payment belongs to.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the user who created this payment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the verification record if this payment has been verified.
     */
    public function verification()
    {
        return $this->hasOne(PaymentVerification::class, 'payment_id');
    }

    /**
     * Check if payment is verified via relationship existence.
     */
    public function getIsVerifiedAttribute(): bool
    {
        return $this->verified || $this->verification()->exists();
    }

    /**
     * Scope for unverified payments.
     */
    public function scopeUnverified($query)
    {
        return $query->where('verified', false);
    }
}
