<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'type', // before, after
        'pic_id',
        'amount_total',
        'amount_service',
        'amount_shipping',
        'payment_method',
        'paid_at',
        'notes',
        'proof_image'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
