<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseInventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'sub_category' => $this->sub_category,
            'unit' => $this->unit,
            'current_stock' => (int)$this->stock,
            'reserved_stock' => (int)$this->reserved_stock,
            'min_stock' => (int)$this->min_stock,
            'available_stock' => (int)$this->getAvailableStock(),
            'unit_price' => (float)$this->price,
            'total_valuation' => (float)($this->stock * $this->price),
            'status' => $this->getStockStatus(),
            'last_updated' => $this->updated_at?->toDateTimeString(),
        ];
    }

    public function with(Request $request): array
    {
        return [
            'status' => 'success',
            'count' => $this->resource instanceof \Illuminate\Support\Collection ? $this->resource->count() : 1,
        ];
    }
}
