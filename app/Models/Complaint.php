<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $work_order_id
 * @property string $customer_name
 * @property string $customer_phone
 * @property string $category
 * @property string $description
 * @property array $photos
 * @property string $status
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Complaint extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'work_order_id',
        'customer_name',
        'customer_phone',
        'category',
        'description',
        'photos',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
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
