<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseServiceTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $wo = $this->workOrder;
        $service = $this->service;

        return [
            'id' => $this->id,
            'work_order_id' => $this->work_order_id,
            'spk_number' => $wo?->spk_number ?? 'N/A',
            'customer_name' => $wo?->customer_name ?? 'N/A',
            'customer_phone' => $wo?->customer_phone,
            'service_name' => $this->custom_service_name ?? ($service?->name ?? 'Custom Service'),
            'category' => $this->category_name ?? ($service?->category ?? '-'),
            'cost' => (float) $this->cost,
            'status' => $this->status ?? ($wo?->status?->value ?? '-'),
            'technician' => $this->technician?->name ?? 'N/A',
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
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
