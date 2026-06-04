<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehousePiutangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'customer' => [
                'id' => $this->customer_id,
                'name' => $this->customer->name ?? null,
                'phone' => $this->customer->phone ?? null,
            ],
            'financials' => [
                'total_amount' => (float)$this->total_amount,
                'paid_amount' => (float)$this->paid_amount,
                'discount' => (float)$this->discount,
                'shipping_cost' => (float)$this->shipping_cost,
                'remaining_balance' => (float)$this->remaining_balance,
                'status' => $this->status, // Belum Bayar, DP/Cicil, Lunas
            ],
            'spk_status' => $this->spk_status, // SELESAI
            'work_orders' => $this->workOrders->map(fn($wo) => [
                'id' => $wo->id,
                'spk_number' => $wo->spk_number,
                'shoe' => [
                    'brand' => $wo->shoe_brand,
                    'type' => $wo->shoe_type,
                    'color' => $wo->shoe_color,
                    'size' => $wo->shoe_size,
                ],
                'services' => $wo->workOrderServices->map(fn($svc) => [
                    'id' => $svc->id,
                    'name' => $svc->custom_service_name ?: ($svc->service->name ?? 'Jasa'),
                    'cost' => (float)$svc->cost,
                ]),
            ]),
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
