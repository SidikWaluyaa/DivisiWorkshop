<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentSyncResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->spk_number_snapshot ?? ($this->invoice ? $this->invoice->invoice_number : '-'),
            'customer_name' => $this->customer_name_snapshot ?? ($this->invoice && $this->invoice->customer ? $this->invoice->customer->name : '-'),
            'customer_phone' => $this->customer_phone_snapshot ?? ($this->invoice && $this->invoice->customer ? $this->invoice->customer->phone : '-'),
            'amount_paid' => (float)$this->amount_total,
            'payment_method' => $this->payment_method,
            'payment_type' => $this->type, // BEFORE (DP/Cicil), AFTER (Pelunasan)
            'total_bill_snapshot' => (float)$this->total_bill_snapshot,
            'balance_snapshot' => (float)$this->balance_snapshot,
            'paid_at' => $this->paid_at ? $this->paid_at->toDateTimeString() : null,
            'notes' => $this->notes,
            'pic_name' => $this->pic ? $this->pic->name : '-',
            'proof_image_url' => $this->proof_image ? url($this->proof_image) : null,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
        ];
    }

    /**
     * Customize the response for the collection.
     */
    public function with(Request $request): array
    {
        return [
            'status' => 'success',
            'count' => $this->resource instanceof \Illuminate\Support\Collection ? $this->resource->count() : 1,
        ];
    }
}
