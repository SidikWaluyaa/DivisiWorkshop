<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CxIssue extends Model
{
    protected $fillable = [
        'work_order_id',
        'reported_by',
        'type',
        'category',
        'description',
        'photos',
        'status',
        'resolution',
        'resolution_notes',
        'suggested_services',
        'recommended_services',
        'desc_upper',
        'desc_sol',
        'desc_kondisi_bawaan',
        'spk_number',
        'customer_phone',
        'customer_name',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'photos' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
