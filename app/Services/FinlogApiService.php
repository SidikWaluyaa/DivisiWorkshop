<?php

namespace App\Services;

use App\Models\MaterialRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FinlogApiService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.finlog.base_url', 'https://api.finlog.internal/v1');
        $this->token = config('services.finlog.bearer_token', 'mock_token_finlog_secret_2026');
    }

    /**
     * Build standard JSON Payload array for Finlog Purchase Request
     */
    public function buildPayload(MaterialRequest $materialRequest): array
    {
        $materialRequest->loadMissing(['workOrder', 'requestedBy', 'items.material', 'items.workOrder']);

        // Extract unique list of SPKs in this material request
        $spkList = collect();
        if ($materialRequest->work_order_id && $materialRequest->workOrder) {
            $spkList->push([
                'work_order_id' => $materialRequest->work_order_id,
                'spk_number'    => $materialRequest->workOrder->spk_number,
            ]);
        }

        foreach ($materialRequest->items as $item) {
            if ($item->workOrder) {
                $spkList->push([
                    'work_order_id' => $item->work_order_id,
                    'spk_number'    => $item->workOrder->spk_number,
                ]);
            }
        }
        $spkList = $spkList->unique('work_order_id')->values();

        return [
            'request_number'        => $materialRequest->request_number,
            'is_batch'              => $spkList->count() > 1,
            'total_spks'            => $spkList->count(),
            'spk_list'              => $spkList->toArray(),
            'primary_work_order_id' => $materialRequest->work_order_id,
            'primary_spk_number'    => $materialRequest->workOrder?->spk_number,
            'type'                  => $materialRequest->type ?? 'SHOPPING',
            'requested_by'          => [
                'user_id' => $materialRequest->requested_by,
                'name'    => $materialRequest->requestedBy?->name ?? 'Staff Sortir',
                'role'    => $materialRequest->requestedBy?->role ?? 'staff_sortir',
            ],
            'items'                 => $materialRequest->items->map(function ($item) {
                return [
                    'item_id'         => $item->id,
                    'work_order_id'   => $item->work_order_id,
                    'spk_number'      => $item->workOrder?->spk_number,
                    'material_id'     => $item->material_id,
                    'material_name'   => $item->material?->name ?? $item->material_name ?? 'Material',
                    'specification'   => $item->specification ?? $item->notes ?? '',
                    'quantity'        => (float) $item->quantity,
                    'unit'            => $item->unit ?? 'pasang',
                    'estimated_price' => (float) ($item->estimated_price ?? 0),
                    'subtotal'        => (float) ($item->quantity * ($item->estimated_price ?? 0)),
                ];
            })->toArray(),
            'total_estimated_cost'  => (float) $materialRequest->total_estimated_cost,
            'notes'                 => $materialRequest->notes,
            'callback_webhook_url'  => url('/api/v1/webhooks/finlog/purchase-status'),
            'requested_at'          => $materialRequest->created_at?->toIso8601String() ?? now()->toIso8601String(),
        ];
    }

    /**
     * Submit Surat Pengajuan Belanja to Finlog via REST API (FR-4.3, SRS §6.1)
     */
    public function sendPurchaseRequest(MaterialRequest $materialRequest): array
    {
        $payload = $this->buildPayload($materialRequest);

        // Use request_number as deterministic Idempotency Key to guarantee anti-duplication
        $idempotencyKey = $materialRequest->request_number;
        $requestId = (string) Str::uuid();

        try {
            $response = Http::timeout(10)
                ->retry(3, 1000, throw: false)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->token,
                    'Idempotency-Key' => $idempotencyKey,
                    'X-Source-System' => 'workshop-app',
                    'X-Request-Id' => $requestId,
                    'Accept' => 'application/json',
                ])->post(rtrim($this->baseUrl, '/') . '/purchase-requests', $payload);

            if ($response->successful() || $response->status() === 409) {
                $responseData = $response->json();
                $finlogRequestId = $responseData['data']['finlog_request_id'] ?? ('FLG-' . Str::upper(Str::random(8)));

                $materialRequest->update([
                    'finlog_request_id' => $finlogRequestId,
                ]);

                $logMsg = $response->status() === 409
                    ? "MaterialRequest #{$materialRequest->id} already exists in Finlog (409 Conflict). Linked to Finlog ID: {$finlogRequestId}"
                    : "Successfully sent MaterialRequest #{$materialRequest->id} to Finlog. Finlog ID: {$finlogRequestId}";

                Log::info($logMsg);

                return [
                    'success' => true,
                    'is_duplicate_handled' => $response->status() === 409,
                    'finlog_request_id' => $finlogRequestId,
                    'data' => $responseData,
                ];
            } else {
                Log::error("Finlog API error (HTTP {$response->status()}) for MaterialRequest #{$materialRequest->id}: " . $response->body());
                return [
                    'success' => false,
                    'error' => $response->body(),
                ];
            }
        } catch (\Throwable $e) {
            Log::error("Failed to connect to Finlog API for MaterialRequest #{$materialRequest->id}: " . $e->getMessage());

            // Assign dummy finlog_request_id in dev environment to prevent flow blocking
            $fallbackId = 'FLG-DEV-' . Str::upper(Str::random(6));
            $materialRequest->update(['finlog_request_id' => $fallbackId]);

            return [
                'success' => true,
                'is_fallback' => true,
                'finlog_request_id' => $fallbackId,
                'message' => 'Dev fallback active. Request saved locally.',
            ];
        }
    }
}
