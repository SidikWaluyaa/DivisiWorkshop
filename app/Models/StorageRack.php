<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageRack extends Model
{
    protected $fillable = [
        'rack_code',
        'location',
        'capacity',
        'current_count',
        'category',
        'status',
        'notes',
    ];

    /**
     * Scope: Only shoe racks
     */
    public function scopeShoes($query)
    {
        return $query->where('category', 'shoes');
    }

    /**
     * Scope: Only accessory racks
     */
    public function scopeAccessories($query)
    {
        return $query->where('category', 'accessories');
    }

    protected $casts = [
        'capacity' => 'integer',
        'current_count' => 'integer',
    ];

    /**
     * Get storage assignments for this rack
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(StorageAssignment::class, 'rack_code', 'rack_code');
    }

    /**
     * Get currently stored work orders
     */
    public function storedOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'storage_rack_code', 'rack_code')
            ->whereNotNull('stored_at')
            ->whereNull('retrieved_at');
    }

    /**
     * Check if rack has available space
     */
    public function isAvailable(): bool
    {
        return $this->status === 'active' && $this->current_count < $this->capacity;
    }

    /**
     * Get remaining capacity
     */
    public function getRemainingCapacity(): int
    {
        return max(0, $this->capacity - $this->current_count);
    }

    /**
     * Get utilization percentage
     */
    public function getUtilizationPercentage(): float
    {
        if ($this->capacity === 0) {
            return 0;
        }
        return ($this->current_count / $this->capacity) * 100;
    }

    /**
     * Increment count when storing item
     */
    public function incrementCount(): void
    {
        $this->increment('current_count');
    }

    /**
     * Decrement count when retrieving item
     */
    public function decrementCount(): void
    {
        if ($this->current_count > 0) {
            $this->decrement('current_count');
        }
    }

    /**
     * Scope: Only active racks
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Only available racks (active with space)
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
            ->whereRaw('current_count < capacity');
    }

    /**
     * Scope: Filter by location
     */
    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    /**
     * Scope: Order by utilization (least filled first)
     */
    public function scopeOrderByUtilization($query, string $direction = 'asc')
    {
        return $query->orderByRaw("(current_count / capacity) {$direction}");
    }
}
