<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseSortirResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'spk_number' => $this->spk_number,
            'customer_name' => $this->customer_name,
            'sortir_category' => $this->sortir_category, // Smart Logic: Siap Produksi, dsb
            'days_in_sortir' => $this->days_in_sortir, // Smart Logic 1: Durasi antrean
            'is_sla_violated' => $this->is_sla_violated, // Smart Logic 1: Bendera merah jika > 3 hari
            'pic_sortir' => [
                'sol' => $this->picSortirSol->name ?? '-',
                'upper' => $this->picSortirUpper->name ?? '-',
            ],
            'entry_date' => $this->waktu?->toDateTimeString(),
            'last_updated' => $this->updated_at?->toDateTimeString(),
        ];
    }

    public function with(Request $request): array
    {
        return [
            'status' => 'success',
            'meta' => [
                'sla_threshold_days' => 3,
                'total_sortir_queue' => $this->resource instanceof \Illuminate\Support\Collection ? $this->resource->count() : 1,
            ]
        ];
    }
}
