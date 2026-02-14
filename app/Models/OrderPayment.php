<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'spk_number_snapshot',
        'type', // before, after
        'pic_id',
        'amount_total',
        'amount_service',
        'amount_shipping',
        'payment_method',
        'paid_at',
        'notes',
        'proof_image',
        'services_snapshot',
        'customer_name_snapshot',
        'customer_phone_snapshot',
        'total_bill_snapshot',
        'discount_snapshot',
        'shipping_cost_snapshot',
        'balance_snapshot'
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
