<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CxIssue extends Model
{
    protected $fillable = [
        'work_order_id',
        'reported_by',
        'type',
        'source',
        'category',
        'description',
        'photos',
        'status',
        'resolution',
        'resolution_notes',
        'suggested_services',
        'recommended_services',
        'rec_service_1',
        'rec_service_2',
        'sug_service_1',
        'sug_service_2',
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

    /**
     * Get the full URLs for the photos
     */
    public function getPhotoUrlsAttribute()
    {
        if (!$this->photos || !is_array($this->photos)) {
            return [];
        }

        return array_map(function ($path) {
            if (!$path) {
                return null;
            }

            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            return asset('storage/' . $path);
        }, $this->photos);
    }
}
