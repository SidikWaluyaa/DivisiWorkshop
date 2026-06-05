<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseSortirSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => 'success',
            'data' => [
                'summary' => $this->resource['metrics'],
                'items' => $this->resource['items'],
                'period' => $this->resource['period'],
                'metadata' => [
                    'last_updated' => $this->resource['last_updated'],
                    'timezone' => config('app.timezone'),
                ]
            ],
            'message' => 'Warehouse sortir summary retrieved successfully.'
        ];
    }
}
