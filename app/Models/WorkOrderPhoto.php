<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderPhoto extends Model
{
    protected $fillable = [
        'work_order_id',
        'step',
        'file_path',
        'is_spk_cover',
        'caption',
        'is_public',
        'user_id'
    ];

    protected $casts = [
        'is_spk_cover' => 'boolean',
        'is_public' => 'boolean'
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
