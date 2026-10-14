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
        'appointment_date',
        'appointment_location',
        'appointment_status',
        'appointment_notes',
    ];

    protected $casts = [
        'photos' => 'array',
        'appointment_date' => 'datetime',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get human-friendly appointment status badge configuration
     */
    public function getAppointmentBadgeAttribute(): array
    {
        return match($this->appointment_status) {
            'COMPLETED' => [
                'label' => 'Selesai Bertemu',
                'bg'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot'   => 'bg-emerald-500',
                'icon'  => '✅',
            ],
            'CANCELLED' => [
                'label' => 'Janji Dibatalkan',
                'bg'    => 'bg-rose-50 text-rose-700 border-rose-200',
                'dot'   => 'bg-rose-500',
                'icon'  => '❌',
            ],
            default => [
                'label' => 'Dijadwalkan',
                'bg'    => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot'   => 'bg-amber-500',
                'icon'  => '⏰',
            ],
        };
    }

    /**
     * Get appointment location readable label
     */
    public function getLocationLabelAttribute(): string
    {
        return match($this->appointment_location) {
            'CALL'  => '📞 Telepon / Video Call',
            'VISIT' => '🏠 Kunjungan Pelanggan',
            default => '🏢 Di Workshop',
        };
    }

    /**
     * Get human-readable category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'QUALITY' => 'Kualitas Hasil',
            'LATE'    => 'Keterlambatan',
            'SERVICE' => 'Pelayanan',
            'DAMAGE'  => 'Kerusakan',
            default   => 'Lainnya',
        };
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

            $cleanPath = ltrim($path, '/');
            if (str_starts_with($cleanPath, 'storage/')) {
                return asset($cleanPath);
            }

            return asset('storage/' . $cleanPath);
        }, $this->photos);
    }
}
