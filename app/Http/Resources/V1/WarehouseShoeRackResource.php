<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseShoeRackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $wo = $this->workOrder;
        $storedAt = $this->stored_at;
        $threeMonthsAgo = now()->subMonths(3);
        $daysStored = $storedAt ? (int) abs(round(now()->diffInDays($storedAt))) : 0;

        return [
            'id' => $wo?->id,
            'spk_number' => $wo?->spk_number ?? 'N/A',
            'customer' => [
                'name' => $wo?->customer_name ?? ($wo?->customer?->name ?? null),
                'phone' => $wo?->customer_phone ?? ($wo?->customer?->phone ?? null),
            ],
            'shoe' => [
                'brand' => $wo?->shoe_brand,
                'type' => $wo?->shoe_type,
                'color' => $wo?->shoe_color,
                'size' => $wo?->shoe_size,
            ],
            'wo_status' => $wo?->status ?? '-',
            'storage' => [
                'rack_code' => $this->rack_code,
                'stored_at' => $storedAt ? $storedAt->toDateTimeString() : null,
                'days_stored' => $daysStored,
                'days_stored_formatted' => $daysStored === 0 ? 'Hari Ini' : $daysStored . ' Hari',
                'is_donation_candidate' => $storedAt ? $storedAt->lte($threeMonthsAgo) : false,
            ],
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
