<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSummaryResource extends JsonResource
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
                'summary' => $this->resource['global'],
                'per_cs' => $this->resource['per_cs'],
                'period' => $this->resource['period'],
                'metadata' => [
                    'last_updated' => $this->resource['last_updated'],
                    'timezone' => config('app.timezone'),
                ]
            ],
            'message' => 'Dashboard summary retrieved successfully.'
        ];
    }
}
