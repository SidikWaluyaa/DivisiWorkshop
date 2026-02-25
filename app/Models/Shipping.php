<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $fillable = [
        'work_order_id',
        'tanggal_masuk',
        'customer_name',
        'customer_phone',
        'spk_number',
        'is_verified',
        'kategori_pengiriman',
        'tanggal_pengiriman',
        'pic',
        'resi_pengiriman',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_pengiriman' => 'date',
        'is_verified' => 'boolean',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
