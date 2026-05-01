<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehousePurchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_number',
        'external_reference',
        'purchase_type',
        'status',
        'total_amount',
        'purchase_date',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(WarehousePurchaseItem::class);
    }

    /**
     * Boot function to auto-generate purchase number
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->purchase_number) {
                $today = now()->format('Ymd');
                $count = static::whereDate('created_at', now()->toDateString())->count() + 1;
                $model->purchase_number = "WH-IN-{$today}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
