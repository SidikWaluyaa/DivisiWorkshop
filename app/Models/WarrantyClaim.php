<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyClaim extends Model
{
    protected $fillable = [
        'work_order_id',
        'customer_name',
        'customer_phone',
        'spk_number',
        'problem_description',
        'problem_photo',
        'google_review_photo',
        'status',
        'rejection_reason',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * Get the original work order.
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the admin user who processed this claim.
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
