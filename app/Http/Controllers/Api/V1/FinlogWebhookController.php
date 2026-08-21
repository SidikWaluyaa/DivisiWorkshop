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
        $data = $payload['data'] ?? $payload;

        $eventId = $request->header('X-Finlog-Event-Id') ?? ($payload['event_id'] ?? null);
        $signature = $request->header('X-Finlog-Signature');

        $requestNumber   = $data['request_number'] ?? null;
        $finlogRequestId = $data['finlog_request_id'] ?? null;
        $workOrderId     = $data['primary_work_order_id'] ?? ($data['work_order_id'] ?? null);
        $status          = $data['status'] ?? null;
        $rejectionReason = $data['rejection_reason'] ?? null;

        Log::info('Finlog Webhook Received:', [
            'event_id'          => $eventId,
            'event'             => $payload['event'] ?? 'status_updated',
            'status'            => $status,
            'request_number'    => $requestNumber,
            'finlog_request_id' => $finlogRequestId,
            'work_order_id'     => $workOrderId,
            'rejection_reason'  => $rejectionReason,
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

        // Map Finlog status to material_requests enum status
        $statusMap = [
            'submitted'         => 'PENDING',
            'pending'           => 'PENDING',
            'approved'          => 'APPROVED',
            'purchased'         => 'PURCHASED',
            'in_transit'        => 'PURCHASED',
            'shipping'          => 'PURCHASED',
            'shipped'           => 'PURCHASED',
            'material_received' => 'RECEIVED',
            'delivered'         => 'RECEIVED',
            'received'          => 'RECEIVED',
            'rejected'          => 'REJECTED',
            'cancelled'         => 'CANCELLED',
        ];

        $targetStatus = $statusMap[$status] ?? null;

        if (!$targetStatus) {
            return response()->json([
                'received' => true,
                'message'  => 'Ignored unknown status: ' . $status,
            ]);
        }

        DB::transaction(function () use ($requestNumber, $finlogRequestId, $workOrderId, $targetStatus, $status, $rejectionReason) {
            // Find material request by request_number, finlog_request_id, or work_order_id
            $materialRequest = null;
            if ($requestNumber) {
                $materialRequest = MaterialRequest::where('request_number', $requestNumber)->first();
            }
            if (!$materialRequest && $finlogRequestId) {
                $materialRequest = MaterialRequest::where('finlog_request_id', $finlogRequestId)->first();
            }
            if (!$materialRequest && $workOrderId) {
                $materialRequest = MaterialRequest::where('work_order_id', $workOrderId)
                    ->latest()
                    ->first();
            }

            if ($materialRequest) {
                $updateData = ['status' => $targetStatus];
                if ($finlogRequestId && empty($materialRequest->finlog_request_id)) {
                    $updateData['finlog_request_id'] = $finlogRequestId;
                }
                if ($rejectionReason && $targetStatus === 'REJECTED') {
                    $updateData['notes'] = "Ditolak Finlog: " . $rejectionReason;
                }

                $materialRequest->update($updateData);

                // Audit log for WorkOrders
                if ($materialRequest->work_order_id && $materialRequest->workOrder) {
                    $materialRequest->workOrder->logs()->create([
                        'step'        => 'PROCUREMENT',
                        'action'      => 'FINLOG_STATUS_UPDATED',
                        'user_id'     => null,
                        'description' => "Status Finlog diperbarui menjadi {$targetStatus}" . ($rejectionReason ? " (Alasan: {$rejectionReason})" : ""),
                    ]);
                }
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
