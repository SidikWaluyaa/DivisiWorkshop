<?php

namespace App\Services;

use App\Contracts\MessagingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SleekFlowService implements MessagingService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('SLEEKFLOW_API_KEY', '');
        $this->baseUrl = env('SLEEKFLOW_BASE_URL', 'https://api.sleekflow.io/v1');
    }

    /**
     * Send a plain text message.
     */
    public function sendMessage(string $phone, string $message): array
    {
        $phone = $this->formatPhoneNumber($phone);
        $url = $this->baseUrl . '/messages';

        try {
            $payload = [
                'phoneNumber' => $phone,
                'type' => 'text',
                'content' => $message,
            ];

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withToken($this->apiKey)->post($url, $payload);

            if ($response->successful()) {
                Log::info("[SleekFlowService] Message sent to $phone");
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error("[SleekFlowService] Failed: " . $response->body());
            return [
                'success' => false,
                'error' => $response->json() ?? $response->body(),
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error("[SleekFlowService] Exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a template-based message (Broadcast).
     */
    public function sendTemplate(string $phone, string $templateId, array $parameters = []): array
    {
        $phone = $this->formatPhoneNumber($phone);
        
        // SleekFlow template sending usually involves 'broadcasts' or 'campaigns'
        // This is a common implementation pattern for SleekFlow
        $url = $this->baseUrl . '/broadcasts';

        try {
            $payload = [
                'phoneNumber' => $phone,
                'templateId' => $templateId,
                'components' => $parameters, // List of values for variables
            ];

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withToken($this->apiKey)->post($url, $payload);

            if ($response->successful()) {
                Log::info("[SleekFlowService] Template $templateId sent to $phone");
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error("[SleekFlowService] Template Failed: " . $response->body());
            return ['success' => false, 'error' => $response->json() ?? $response->body()];
        } catch (\Exception $e) {
            Log::error("[SleekFlowService] Template Exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}
