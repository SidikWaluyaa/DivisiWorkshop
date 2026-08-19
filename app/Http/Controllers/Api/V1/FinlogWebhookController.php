<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinlogWebhookController extends Controller
{
    /**
     * Handle Webhook updates from Finlog (FR-4.4, FR-4.6, SRS §6.2)
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        $eventId = $request->header('X-Finlog-Event-Id') ?? ($payload['event_id'] ?? null);
        $signature = $request->header('X-Finlog-Signature');

        Log::info('Finlog Webhook Received:', [
            'event_id' => $eventId,
            'status' => $payload['status'] ?? null,
            'work_order_id' => $payload['work_order_id'] ?? null,
            'finlog_request_id' => $payload['finlog_request_id'] ?? null,
        ]);

        // Optional HMAC signature check if secret is configured
        $secret = config('services.finlog.webhook_secret');
        if ($secret && $signature) {
            $computed = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
            if (!hash_equals($computed, $signature)) {
                Log::warning('Finlog Webhook Signature Mismatch');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $finlogRequestId = $payload['finlog_request_id'] ?? null;
        $workOrderId = $payload['work_order_id'] ?? null;
        $status = $payload['status'] ?? null;

        // Map Finlog status to material_requests enum status
        $statusMap = [
            'submitted' => 'PENDING',
            'approved' => 'APPROVED',
            'rejected' => 'REJECTED',
            'purchased' => 'PURCHASED',
            'material_received' => 'RECEIVED',
            'cancelled' => 'CANCELLED',
        ];

        $targetStatus = $statusMap[$status] ?? null;

        if (!$targetStatus) {
            return response()->json([
                'received' => true,
                'message' => 'Ignored unknown status: ' . $status,
            ]);
        }

        DB::transaction(function () use ($finlogRequestId, $workOrderId, $targetStatus, $status, $payload, $eventId) {
            // Find material request by finlog_request_id or work_order_id
            $materialRequest = null;
            if ($finlogRequestId) {
                $materialRequest = MaterialRequest::where('finlog_request_id', $finlogRequestId)->first();
            }
            if (!$materialRequest && $workOrderId) {
                $materialRequest = MaterialRequest::where('work_order_id', $workOrderId)
                    ->latest()
                    ->first();
            }

            if ($materialRequest) {
                $materialRequest->update(['status' => $targetStatus]);
            }

            // Flag material arrival on related WorkOrders, requiring manual staff verification button to push to Production
            if ($status === 'material_received') {
                $workOrdersToFlag = collect();
                if ($materialRequest) {
                    $materialRequest->loadMissing('items.workOrder');
                    foreach ($materialRequest->items as $item) {
                        if ($item->workOrder) {
                            $workOrdersToFlag->push($item->workOrder);
                        }
                    }
                }
                if ($workOrderId && $workOrdersToFlag->where('id', $workOrderId)->isEmpty()) {
                    $wo = WorkOrder::find($workOrderId);
                    if ($wo) $workOrdersToFlag->push($wo);
                }

                foreach ($workOrdersToFlag->unique('id') as $workOrder) {
                    $workOrder->material_arrival_date = now();
                    $workOrder->save();

                    $workOrder->logs()->create([
                        'user_id' => 1,
                        'step' => 'SORTIR_BELANJA',
                        'action' => 'FINLOG_MATERIAL_ARRIVED',
                        'description' => "Material pengajuan Finlog (#{$finlogRequestId}) telah tiba di Workshop. Menunggu verifikasi fisik & konfirmasi tombol 'Terima Material' oleh staff.",
                    ]);

                    Log::info("Finlog Webhook: WorkOrder #{$workOrder->id} ({$workOrder->spk_number}) material arrived. Awaiting manual staff confirmation.");
                }
            }
        });

        return response()->json([
            'received' => true,
            'event_id' => $eventId,
            'processed_at' => now()->toIso8601String(),
        ]);
    }
}
