<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CxAfterConfirmation extends Model
{
    protected $fillable = [
        'work_order_id',
        'entered_at',
        'contacted_at',
        'pic_id',
        'response',
        'notes'
    ];

    protected $casts = [
        'entered_at' => 'datetime',
        'contacted_at' => 'datetime',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
