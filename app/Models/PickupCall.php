<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupCall extends Model
{
    protected $fillable = [
        'work_order_id',
        'status',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
}
