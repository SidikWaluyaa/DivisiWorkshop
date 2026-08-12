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

            // FR-4.6: Auto-Otomasi status SPK saat "material_received"
            if ($status === 'material_received' && $workOrderId) {
                $workOrder = WorkOrder::find($workOrderId);
                if ($workOrder) {
                    // Update material arrival date
                    $workOrder->material_arrival_date = now();
                    $workOrder->save();

                    // Use WorkflowService for proper status transition with audit trail
                    try {
                        $workflow = app(\App\Services\WorkflowService::class);
                        $workflow->updateStatus(
                            $workOrder,
                            WorkOrderStatus::PRODUCTION,
                            "Material Diterima dari Finlog (Event: {$eventId}, Req: {$finlogRequestId}). SPK otomatis berlanjut ke OTW Produksi."
                        );
                    } catch (\Exception $e) {
                        // If transition validation fails, log and continue gracefully
                        Log::warning("FR-4.6: WorkflowService transition failed for WO #{$workOrder->id}: " . $e->getMessage());
                        
                        // Fallback: direct status update with log
                        $workOrder->status = WorkOrderStatus::PRODUCTION;
                        $workOrder->save();
                        
                        $workOrder->logs()->create([
                            'user_id' => 1,
                            'step' => 'SORTIR_BELANJA',
                            'action' => 'AUTO_FORWARD_PRODUKSI',
                            'description' => "Material Diterima dari Finlog (Event: {$eventId}, Req: {$finlogRequestId}). SPK otomatis berlanjut ke OTW Produksi (fallback).",
                        ]);
                    }

                    Log::info("FR-4.6 Triggered: WorkOrder #{$workOrder->id} ({$workOrder->spk_number}) auto-forwarded to Production.");
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
