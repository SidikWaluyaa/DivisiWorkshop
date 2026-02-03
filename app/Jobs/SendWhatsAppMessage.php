<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\CekatService;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessage implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $phone,
        protected string $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(\App\Contracts\MessagingService $messagingService): void
    {
        Log::info("[Queue] Processing WhatsApp to: {$this->phone}");
        
        $response = $messagingService->sendMessage($this->phone, $this->message);

        if (!$response['success']) {
            Log::error("[Queue] Failed to send WhatsApp: " . json_encode($response));
            // Optional: Release job back to queue to retry later
            // $this->release(30); 
            throw new \Exception("Failed to send WhatsApp message via Cekat API.");
        }
        
        Log::info("[Queue] WhatsApp Sent Successfully.");
    }
}
