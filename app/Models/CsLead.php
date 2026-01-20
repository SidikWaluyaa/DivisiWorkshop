<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsLead extends Model
{
    use HasFactory;

    const STATUS_NEW = 'NEW';
    const STATUS_KONSULTASI = 'KONSULTASI';
    const STATUS_INVEST_GREETING = 'INV_GREETING';
    const STATUS_INVEST_KONSULTASI = 'INV_KONSULTASI';
    const STATUS_CLOSED = 'CLOSED';

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_city',
        'customer_province',
        'status', // NEW, KONSULTASI, INV_GREETING, INV_KONSULTASI, CLOSING
        'cs_id',
        'last_updated_at',
        'notes'
    ];

    protected $casts = [
        'last_updated_at' => 'datetime',
    ];

    // Relationships
    public function cs()
    {
        return $this->belongsTo(User::class, 'cs_id');
    }
}
