<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderWarranty extends Model
{
    protected $fillable = [
        'work_order_id',
        'garansi_spk_number',
        'description',
        'status',
        'created_by',
        'finished_by',
        'finished_at',
        'photos',
    ];

    protected $casts = [
        'finished_at' => 'datetime',
        'photos' => 'array',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function finisher()
    {
        return $this->belongsTo(User::class, 'finished_by');
    }

    public function reworkWorkOrder()
    {
        return $this->hasOne(WorkOrder::class, 'spk_number', 'garansi_spk_number');
    }
}
