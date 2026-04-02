<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceSyncResource extends JsonResource
{
    /**
     * Transform the resource into an array to match legacy sync_finance.php format perfectly.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Status mapping matching line 108-111 of sync_finance.php
        $botStatus = 'BB';
        if ($this->status === 'Lunas') $botStatus = 'L';
        elseif ($this->status === 'DP/Cicil') $botStatus = 'BL';

        return [
            'status' => 'IN_PROGRESS', // Legacy static value
            'spk_number' => $this->invoice_number,
            'customer_name' => $this->customer->name ?? '-',
            'customer_phone' => $this->customer->phone ?? '-',
            'status_pembayaran' => $botStatus,
            'spk_status' => $this->spk_status ?? 'BELUM SELESAI',
            'amount_paid' => (float)$this->paid_amount,
            'total_bill' => (float)$this->total_amount,
            'discount' => (float)$this->discount,
            'shipping_cost' => (float)($this->shipping_cost ?? 0),
            'remaining_balance' => (float)$this->remaining_balance,
            'invoice_awal_url' => $this->invoice_awal_url,
            'invoice_akhir_url' => $this->invoice_akhir_url,
            'estimasi_selesai' => $this->estimasi_selesai?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
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
