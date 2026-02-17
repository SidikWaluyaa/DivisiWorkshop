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
        'is_primary_reference',
        'caption',
        'is_public',
        'user_id'
    ];

    protected $casts = [
        'is_spk_cover' => 'boolean',
        'is_primary_reference' => 'boolean',
        'is_public' => 'boolean'
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        if (filter_var($this->file_path, FILTER_VALIDATE_URL)) {
            return $this->file_path;
        }

        return asset('storage/' . $this->file_path);
    }

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
