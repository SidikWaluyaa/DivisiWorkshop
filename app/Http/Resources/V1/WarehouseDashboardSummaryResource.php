<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseDashboardSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'summary' => $this->resource['metrics'],
            'qc_analytics' => $this->resource['qc_analytics'],
            'efficiency' => $this->resource['efficiency'],
            'inventory' => $this->resource['inventory'],
            'storage' => $this->resource['storage'],
            'queues' => $this->resource['queues'],
            'period' => $this->resource['period'],
            'metadata' => [
                'last_updated' => $this->resource['last_updated'],
                'timezone' => config('app.timezone'),
            ]
        ];

        // Kamus MoM (Tahap 2): hanya ada bila request mengirim as_of_date.
        if (isset($this->resource['mom'])) {
            $data += $this->resource['mom'];
        }

        return [
            'status' => 'success',
            'data' => $data,
            'message' => 'Warehouse dashboard summary retrieved successfully.'
        ];
    }
}
