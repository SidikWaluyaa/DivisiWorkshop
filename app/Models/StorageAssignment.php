<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorageAssignment extends Model
{
    protected $fillable = [
        'work_order_id',
        'rack_code',
        'stored_at',
        'retrieved_at',
        'stored_by',
        'retrieved_by',
        'item_type',
        'status',
        'notes',
    ];

    /**
     * Scope: Only shoe assignments
     */
    public function scopeShoes($query)
    {
        return $query->where('item_type', 'shoes');
    }

    /**
     * Scope: Only accessory assignments
     */
    public function scopeAccessories($query)
    {
        return $query->where('item_type', 'accessories');
    }

    protected $casts = [
        'stored_at' => 'datetime',
        'retrieved_at' => 'datetime',
    ];

    /**
     * Get the work order for this assignment
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the storage rack
     */
    public function rack(): BelongsTo
    {
        return $this->belongsTo(StorageRack::class, 'rack_code', 'rack_code');
    }

    /**
     * Get the user who stored the item
     */
    public function storedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'stored_by');
    }

    /**
     * Get the user who retrieved the item
     */
    public function retrievedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retrieved_by');
    }

    /**
     * Get storage duration in days
     */
    public function getStorageDurationDays(): int
    {
        $endDate = $this->retrieved_at ?? now();
        return $this->stored_at->diffInDays($endDate);
    }

    /**
     * Check if item is currently stored
     */
    public function isStored(): bool
    {
        return $this->status === 'stored' && is_null($this->retrieved_at);
    }

    /**
     * Check if item is overdue (stored > X days)
     */
    public function isOverdue(int $days = 7): bool
    {
        if (!$this->isStored()) {
            return false;
        }
        return $this->stored_at->diffInDays(now()) > $days;
    }

    /**
     * Scope: Currently stored items
     */
    public function scopeStored($query)
    {
        return $query->where('status', 'stored')
            ->whereNull('retrieved_at');
    }

    /**
     * Scope: Retrieved items
     */
    public function scopeIsRetrieved($query)
    {
        return $query->where('status', 'retrieved')
            ->whereNotNull('retrieved_at');
    }

    /**
     * Scope: Filter by rack
     */
    public function scopeByRack($query, string $rackCode)
    {
        return $query->where('rack_code', $rackCode);
    }

    /**
     * Scope: Overdue items
     */
    public function scopeOverdue($query, int $days = 7)
    {
        return $query->where('status', 'stored')
            ->whereNull('retrieved_at')
            ->where('stored_at', '<=', now()->subDays($days));
    }

    /**
     * Scope: Recent assignments
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('stored_at', '>=', now()->subDays($days));
    }
}
