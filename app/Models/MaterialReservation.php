<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialReservation extends Model
{
    protected $fillable = [
        'material_id',
        'oto_id',
        'work_order_id',
        'quantity',
        'type',
        'status',
        'expires_at',
        'confirmed_at',
        'released_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    /**
     * Get the material that is reserved
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the OTO that owns this reservation
     */
    public function oto(): BelongsTo
    {
        return $this->belongsTo(OTO::class, 'oto_id');
    }

    /**
     * Get the work order that owns this reservation
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Check if reservation is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now() && $this->status === 'ACTIVE';
    }

    /**
     * Check if reservation is soft (temporary)
     */
    public function isSoft(): bool
    {
        return $this->type === 'SOFT';
    }

    /**
     * Check if reservation is hard (confirmed)
     */
    public function isHard(): bool
    {
        return $this->type === 'HARD';
    }

    /**
     * Convert soft reservation to hard
     */
    public function confirmReservation(): void
    {
        $this->update([
            'type' => 'HARD',
            'status' => 'CONFIRMED',
            'confirmed_at' => now(),
            'expires_at' => null,
        ]);
    }

    /**
     * Release the reservation
     */
    public function release(): void
    {
        $this->update([
            'status' => 'RELEASED',
            'released_at' => now(),
        ]);

        // Decrement reserved stock
        $this->material->decrement('reserved_stock', $this->quantity);
    }

    /**
     * Scope for active reservations
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    /**
     * Scope for soft reservations
     */
    public function scopeSoft($query)
    {
        return $query->where('type', 'SOFT');
    }

    /**
     * Scope for hard reservations
     */
    public function scopeHard($query)
    {
        return $query->where('type', 'HARD');
    }

    /**
     * Scope for expired reservations
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'ACTIVE')
                     ->where('expires_at', '<=', now());
    }
}
