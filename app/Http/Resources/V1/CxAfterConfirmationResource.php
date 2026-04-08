<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CxAfterConfirmationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'spk_number' => $this->workOrder->spk_number ?? null,
            'customer_name' => $this->workOrder->customer_name ?? null,
            'customer_phone' => $this->workOrder->customer_phone ?? null,
            'brand_color' => ($this->workOrder->shoe_brand ?? '') . ' - ' . ($this->workOrder->shoe_color ?? ''),
            'entered_at' => $this->entered_at ? $this->entered_at->format('Y-m-d H:i:s') : null,
            'response' => $this->response ?? 'Belum Direspon',
            'pic_name' => $this->pic->name ?? '',
            'contacted_at' => $this->contacted_at ? $this->contacted_at->format('Y-m-d H:i:s') : null,
            'notes' => $this->notes ?? '',
        ];
    }
}
