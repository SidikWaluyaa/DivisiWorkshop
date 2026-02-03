<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Services\WorkflowService;
use App\Enums\WorkOrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SleekFlowWebhookController extends Controller
{
    protected WorkflowService $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Main entry point for SleekFlow Webhooks.
     */
    public function handle(Request $request)
    {
        // 1. Log the incoming payload for debugging
        Log::info('[SleekFlow Webhook] Received payload: ', $request->all());

        // 2. Security: Verify Signature (Optional but recommended for Big 4 standards)
        // $this->verifySignature($request);

        $event = $request->input('event'); // e.g., 'message.created'
        $data = $request->input('data');

        if (!$data) {
            return response()->json(['message' => 'No data provided'], 400);
        }

        try {
            switch ($event) {
                case 'message.created':
                    return $this->handleMessageCreated($data);
                
                // Add more events as needed
                default:
                    Log::info("[SleekFlow Webhook] Unhandled event: {$event}");
                    return response()->json(['message' => 'Event unhandled'], 200);
            }
        } catch (\Exception $e) {
            Log::error("[SleekFlow Webhook] Error processing webhook: " . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    /**
     * Handle incoming messages (Text or Button Clicks).
     */
    protected function handleMessageCreated(array $data)
    {
        $senderPhone = $data['phoneNumber'] ?? null;
        $messageType = $data['type'] ?? 'text'; // 'text' or 'interactive'
        $content = $data['content'] ?? '';

        if (!$senderPhone) {
            return response()->json(['message' => 'Phone number missing'], 400);
        }

        // Clean phone number format
        $cleanPhone = preg_replace('/[^0-9]/', '', $senderPhone);
        if (str_starts_with($cleanPhone, '628')) {
            // Standard format
        } elseif (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        // 3. Find associated WorkOrder
        // We look for the LATEST active order for this phone number
        $order = WorkOrder::where('customer_phone', 'LIKE', "%" . substr($cleanPhone, 2) . "%")
            ->whereNotIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::BATAL])
            ->latest()
            ->first();

        if (!$order) {
            Log::info("[SleekFlow Webhook] No active order found for phone: {$cleanPhone}");
            return response()->json(['message' => 'Order not found'], 200);
        }

        // 4. Handle Interactive Button Clicks
        if ($messageType === 'interactive' || $this->isButtonResponse($content)) {
            return $this->handleButtonAction($order, $content);
        }

        // 5. Log Plain Text Message to Order History
        $this->logMessageToOrder($order, $content);

        return response()->json(['message' => 'Message processed'], 200);
    }

    /**
     * Check if message content matches expected button values.
     */
    protected function isButtonResponse(string $content): bool
    {
        $triggers = ['SETUJU', 'TOLAK', 'GAS', 'BATALKAN'];
        return in_array(strtoupper(trim($content)), $triggers);
    }

    /**
     * Logic for automated status updates based on customer interaction.
     */
    protected function handleButtonAction(WorkOrder $order, string $content)
    {
        $action = strtoupper(trim($content));

        DB::transaction(function () use ($order, $action) {
            switch ($action) {
                case 'SETUJU':
                case 'GAS':
                    if ($order->status === WorkOrderStatus::ASSESSMENT || $order->status === WorkOrderStatus::WAITING_PAYMENT) {
                        $this->workflowService->updateStatus(
                            $order, 
                            WorkOrderStatus::PREPARATION, 
                            "Otomatis: Customer setuju via WhatsApp."
                        );
                    }
                    break;

                case 'TOLAK':
                case 'BATALKAN':
                    $this->workflowService->updateStatus(
                        $order, 
                        WorkOrderStatus::BATAL, 
                        "Otomatis: Customer membatalkan via WhatsApp."
                    );
                    break;
            }
        });

        return response()->json(['message' => "Action {$action} handled for Order #{$order->spk_number}"], 200);
    }

    /**
     * Save customer chat into the work order logs for CS visibility.
     */
    protected function logMessageToOrder(WorkOrder $order, string $content)
    {
        $order->logs()->create([
            'step' => 'CHAT',
            'action' => 'CUSTOMER_MESSAGE_RECEIVED',
            'user_id' => null, // System / Customer
            'description' => "Pesan Baru via WA: \"{$content}\"",
        ]);
        
        Log::info("[SleekFlow Webhook] Customer message logged for Order #{$order->spk_number}");
    }

    /**
     * Optional: Verify SleekFlow API Key / Signature.
     */
    protected function verifySignature(Request $request)
    {
        $expectedKey = env('SLEEKFLOW_WEBHOOK_SECRET');
        $receivedKey = $request->header('X-SleekFlow-Verification-Token');

        if ($expectedKey && $expectedKey !== $receivedKey) {
            abort(403, 'Unauthorized Webhook Request');
        }
    }
}
