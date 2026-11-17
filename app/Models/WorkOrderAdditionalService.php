<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrderAdditionalService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'work_order_id',
        'spk_number',
        'customer_name',
        'customer_phone',
        'service_id',
        'service_name',
        'category_name',
        'cost',
        'notes',
        'status',
        'sol_by',
        'sol_started_at',
        'sol_completed_at',
        'upper_by',
        'upper_started_at',
        'upper_completed_at',
        'treatment_by',
        'treatment_started_at',
        'treatment_completed_at',
        'started_at',
        'completed_at',
        'created_by',
        'completed_by',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'sol_started_at' => 'datetime',
        'sol_completed_at' => 'datetime',
        'upper_started_at' => 'datetime',
        'upper_completed_at' => 'datetime',
        'treatment_started_at' => 'datetime',
        'treatment_completed_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function solBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sol_by');
    }

    public function upperBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'upper_by');
    }

    public function treatmentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'treatment_by');
    }
}
