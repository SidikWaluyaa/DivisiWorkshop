<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseDisbursement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'disbursement_number',
        'external_reference',
        'status',
        'total_amount',
        'disbursement_date',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'disbursement_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(WarehouseDisbursementItem::class);
    }

    /**
     * Boot function to auto-generate disbursement number
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->disbursement_number) {
                $today = now()->format('Ymd');
                $count = static::whereDate('created_at', now()->toDateString())->count() + 1;
                $model->disbursement_number = "WH-OUT-{$today}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
