<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseDisbursementItem extends Model
{
    protected $fillable = [
        'warehouse_disbursement_id',
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

    public function disbursement()
    {
        return $this->belongsTo(WarehouseDisbursement::class, 'warehouse_disbursement_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
