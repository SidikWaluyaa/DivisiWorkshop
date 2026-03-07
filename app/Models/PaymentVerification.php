<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'mutation_id',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Get the invoice payment that was verified.
     */
    public function payment()
    {
        return $this->belongsTo(InvoicePayment::class, 'payment_id');
    }

    /**
     * Get the bank mutation used for verification.
     */
    public function mutation()
    {
        return $this->belongsTo(BankMutation::class, 'mutation_id');
    }

    /**
     * Get the user who performed the verification.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
