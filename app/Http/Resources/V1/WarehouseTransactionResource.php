<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'material_name' => $this->material->name ?? 'N/A',
            'type' => $this->type, // IN, OUT
            'quantity' => (int)$this->quantity,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'notes' => $this->notes,
            'user_name' => $this->user->name ?? 'System',
            'created_at' => $this->created_at?->toDateTimeString(),
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
