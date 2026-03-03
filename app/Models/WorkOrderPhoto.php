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

        // 1. Clean up weird whitespaces/newlines from DB (CRITICAL FIX)
        $path = trim(preg_replace('/\s+/', '', $this->file_path));

        // 2. If it's a full URL string stored in DB, just return it directly 
        // after cleaning up the whitespace.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // 3. If it's a relative path starting with 'storage/'
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        return asset('storage/' . $path);
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
