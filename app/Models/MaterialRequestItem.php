<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialRequestItem extends Model
{
    protected $fillable = [
        'material_request_id',
        'material_id',
        'material_name',
        'specification',
        'quantity',
        'unit',
        'estimated_price',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'estimated_price' => 'decimal:2',
    ];

    // Relationships
    public function materialRequest(): BelongsTo
    {
        return $this->belongsTo(MaterialRequest::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    // Helper methods
    public function getSubtotal(): float
    {
        return $this->quantity * $this->estimated_price;
    }

    public function isCustomMaterial(): bool
    {
        return $this->material_id === null;
    }
}
