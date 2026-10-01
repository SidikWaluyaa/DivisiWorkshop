<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WorkOrderRndProgress extends Model
{
    use HasFactory;

    protected $table = 'work_order_rnd_progress';

    protected $fillable = [
        'work_order_id',
        'user_id',
        'stage_title',
        'notes',
        'photo_path',
        'result_status',
        'report_token',
        'upload_token',
        'report_url',
    ];

    /**
     * Relasi ke WorkOrder pemilik jurnal
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    /**
     * Relasi ke User teknisi yang mengunggah
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Boot model untuk memastikan token unik dibuat otomatis
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->report_token)) {
                $model->report_token = Str::random(64);
            }
            if (empty($model->upload_token)) {
                $model->upload_token = Str::random(64);
            }
        });
    }
}
