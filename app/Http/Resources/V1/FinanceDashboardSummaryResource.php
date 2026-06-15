<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceDashboardSummaryResource extends JsonResource
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
                'metrics' => $this->resource['metrics'],
                'status_breakdown' => $this->resource['status_breakdown'],
                'overdue_invoices' => $this->resource['overdue_invoices'],
                'chart_data' => $this->resource['chart_data'],
                'recent_payments' => $this->resource['recent_payments'],
                'period' => $this->resource['period'],
                'metadata' => [
                    'last_updated' => $this->resource['last_updated'],
                    'timezone' => config('app.timezone'),
                ]
            ],
            'message' => 'Finance dashboard summary retrieved successfully.'
        ];
    }
}
