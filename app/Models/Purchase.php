<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'po_number',
        'supplier_name',
        'quality_rating',
        'material_id',
        'quantity',
        'unit_price',
        'total_price',
        'status',
        'payment_status',
        'paid_amount',
        'order_date',
        'received_date',
        'due_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'received_date' => 'date',
        'due_date' => 'date',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getOutstandingAmountAttribute()
    {
        return $this->total_price - $this->paid_amount;
    }
}
