<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehousePurchaseItem extends Model
{
    protected $fillable = [
        'warehouse_purchase_id',
        'material_id',
        'spk_number',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function purchase()
    {
        return $this->belongsTo(WarehousePurchase::class, 'warehouse_purchase_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
