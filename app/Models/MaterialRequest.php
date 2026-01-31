<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialRequest extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'request_number',
        'work_order_id',
        'oto_id',
        'requested_by',
        'type',
        'status',
        'notes',
        'total_estimated_cost',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'total_estimated_cost' => 'decimal:2',
    ];

    // Relationships
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function oto(): BelongsTo
    {
        return $this->belongsTo(OTO::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MaterialRequestItem::class);
    }

    // Helper methods
    public function isShopping(): bool
    {
        return $this->type === 'SHOPPING';
    }

    public function isProductionPO(): bool
    {
        return $this->type === 'PRODUCTION_PO';
    }

    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => 'APPROVED',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update(['status' => 'REJECTED']);
    }

    public function markAsPurchased(): void
    {
        $this->update(['status' => 'PURCHASED']);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'CANCELLED']);
    }

    // Generate unique request number
    public static function generateRequestNumber(): string
    {
        $year = now()->year;
        $lastRequest = self::withTrashed()->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastRequest ? (int) substr($lastRequest->request_number, -4) + 1 : 1;

        return 'REQ-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // Scopes
    public function scopeShopping($query)
    {
        return $query->where('type', 'SHOPPING');
    }

    public function scopeProductionPO($query)
    {
        return $query->where('type', 'PRODUCTION_PO');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }
}
