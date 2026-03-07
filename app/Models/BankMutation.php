<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'invoice_number',
        'amount',
        'description',
        'bank_code',
        'mutation_type',
        'used',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'used' => 'boolean',
    ];

    /**
     * Get the verification record if this mutation has been used.
     */
    public function verification()
    {
        return $this->hasOne(PaymentVerification::class, 'mutation_id');
    }

    /**
     * Scope for unused (available) mutations.
     */
    public function scopeUnused($query)
    {
        return $query->where('used', false);
    }

    /**
     * Scope for credit (incoming) mutations only.
     */
    public function scopeCreditsOnly($query)
    {
        return $query->where('mutation_type', 'CR');
    }
}
