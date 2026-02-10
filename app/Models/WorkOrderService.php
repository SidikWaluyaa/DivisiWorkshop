<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WorkOrderService extends Pivot
{
    protected $table = 'work_order_services';
    public $incrementing = true;
    
    protected $fillable = [
        'work_order_id',
        'service_id',
        'cost',
        'status',
        'technician_id',
        'custom_service_name',
        'category_name',
        'service_details',
        'notes'
    ];

    protected $casts = [
        'service_details' => 'array',
        'cost' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
