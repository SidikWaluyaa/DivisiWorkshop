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
        $assignment = $this->storageAssignments->first();
        $storedAt = $assignment ? $assignment->stored_at : null;
        $threeMonthsAgo = now()->subMonths(3);
        $daysStored = $storedAt ? (int) abs(round(now()->diffInDays($storedAt))) : 0;

        return [
            'id' => $this->id,
            'spk_number' => $this->spk_number,
            'customer' => [
                'name' => $this->customer_name ?? ($this->customer->name ?? null),
                'phone' => $this->customer_phone ?? ($this->customer->phone ?? null),
            ],
            'shoe' => [
                'brand' => $this->shoe_brand,
                'type' => $this->shoe_type,
                'color' => $this->shoe_color,
                'size' => $this->shoe_size,
            ],
            'storage' => [
                'rack_code' => $assignment ? $assignment->rack_code : null,
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
