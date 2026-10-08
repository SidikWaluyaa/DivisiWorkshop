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
        'type',
        'created_by',
        'refund_bank_name',
        'refund_account_number',
        'refund_account_name',
        'proof_image',
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
     * Check if this payment is a refund / reduction / compensation.
     */
    public function isRefund(): bool
    {
        return in_array(strtoupper($this->type ?? ''), ['REFUND', 'KOMPENSASI', 'DISKON_PENYESUAIAN']);
    }

    public function getIsRefundAttribute(): bool
    {
        return $this->isRefund();
    }

    /**
     * Scope for unverified payments.
     */
    public function scopeUnverified($query)
    {
        return $query->where('verified', false);
    }
}
