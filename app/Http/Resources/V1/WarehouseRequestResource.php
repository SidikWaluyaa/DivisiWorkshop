<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_number' => $this->request_number,
            'type' => $this->type,
            'status' => $this->status,
            'item_count' => $this->items->count(),
            'total_estimated_cost' => (float)$this->total_estimated_cost,
            'requested_by' => $this->requestedBy->name ?? '-',
            'approved_at' => $this->approved_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'items' => $this->items->map(fn($item) => [
                'material_name' => $item->material->name ?? 'N/A',
                'quantity' => (int)$item->quantity,
                'unit' => $item->material->unit ?? 'pcs',
            ]),
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
