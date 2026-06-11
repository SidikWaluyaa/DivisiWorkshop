<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerPortalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customer = $this->resource['customer'];
        $workOrders = $this->resource['work_orders'];

        return [
            'status' => 'success',
            'data' => [
                'customer' => $customer ? [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'address' => $customer->address,
                    'city' => $customer->city,
                    'province' => $customer->province,
                    'district' => $customer->district,
                    'village' => $customer->village,
                    'postal_code' => $customer->postal_code,
                ] : [
                    'name' => 'Pelanggan Umum',
                    'phone' => $this->resource['query_phone'],
                    'email' => null,
                    'address' => null,
                    'city' => null,
                    'province' => null,
                    'district' => null,
                    'village' => null,
                    'postal_code' => null,
                ],
                'work_orders' => $workOrders->map(function($wo) {
                    return [
                        'id' => $wo->id,
                        'spk_number' => $wo->spk_number,
                        'shoe_brand' => $wo->shoe_brand,
                        'shoe_type' => $wo->shoe_type,
                        'shoe_color' => $wo->shoe_color,
                        'shoe_size' => $wo->shoe_size,
                        'category' => $wo->category,
                        'status' => [
                            'code' => $wo->status->value ?? $wo->status,
                            'label' => method_exists($wo->status, 'label') ? $wo->status->label() : ($wo->status->value ?? $wo->status),
                        ],
                        'priority' => $wo->priority,
                        'notes' => $wo->notes,
                        'entry_date' => $wo->entry_date ? $wo->entry_date->toDateTimeString() : null,
                        'estimation_date' => $wo->estimation_date ? $wo->estimation_date->toDateTimeString() : null,
                        'finished_date' => $wo->finished_date ? $wo->finished_date->toDateTimeString() : null,
                        'taken_date' => $wo->taken_date ? $wo->taken_date->toDateTimeString() : null,
                        'payment' => [
                            'status' => $wo->status_pembayaran,
                            'total_amount' => (float) ($wo->total_transaksi ?? 0),
                            'paid_amount' => (float) ($wo->total_paid ?? 0),
                            'remaining_balance' => (float) ($wo->sisa_tagihan ?? 0),
                        ],
                        'services' => $wo->workOrderServices->map(function($wos) {
                            return [
                                'id' => $wos->id,
                                'service_id' => $wos->service_id,
                                'service_name' => $wos->custom_service_name ?: ($wos->service->name ?? 'Layanan'),
                                'category_name' => $wos->category_name,
                                'cost' => (float) $wos->cost,
                                'notes' => $wos->notes,
                            ];
                        }),
                        'photos' => $wo->photos->map(function($photo) {
                            return [
                                'id' => $photo->id,
                                'step' => $photo->step,
                                'photo_url' => $photo->photo_url,
                                'caption' => $photo->caption,
                                'is_spk_cover' => (bool) $photo->is_spk_cover,
                                'is_public' => (bool) $photo->is_public,
                            ];
                        }),
                    ];
                }),
            ],
            'message' => 'Customer portal orders retrieved successfully.'
        ];
    }
}
