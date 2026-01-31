<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'type',
        'category',
        'sub_category',
        'size',
        'stock',
        'unit',
        'price',
        'min_stock',
        'reserved_stock',
        'status',
        'pic_user_id',
    ];

    protected $casts = [
        'stock' => 'integer',
        'reserved_stock' => 'integer',
        'min_stock' => 'integer',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function materialRequests()
    {
        return $this->hasMany(MaterialRequestItem::class);
    }

    public function reservations()
    {
        return $this->hasMany(MaterialReservation::class);
    }

    // Helper methods for category
    public function isShopping(): bool
    {
        return strtoupper($this->category) === 'SHOPPING';
    }

    public function isProduction(): bool
    {
        return strtoupper($this->category) === 'PRODUCTION';
    }

    // Stock management helpers
    public function getAvailableStock(): int
    {
        return max(0, $this->stock - ($this->reserved_stock ?? 0));
    }

    public function isStockAvailable(int $quantity): bool
    {
        return $this->getAvailableStock() >= $quantity;
    }

    public function isLowStock(): bool
    {
        return $this->getAvailableStock() <= $this->min_stock;
    }

    public function getStockStatus(): string
    {
        if ($this->isShopping()) {
            return 'N/A'; // Shopping materials don't track stock
        }

        $available = $this->getAvailableStock();
        
        if ($available <= 0) {
            return 'Out of Stock';
        } elseif ($this->isLowStock()) {
            return 'Low Stock';
        } else {
            return 'Available';
        }
    }

}
