<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CekatService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('CEKAT_API_KEY');
        $this->baseUrl = env('CEKAT_BASE_URL', 'https://api.cekat.ai/api'); 
    }

    /**
     * Send a WhatsApp message.
     *
     * @param string $phone
     * @param string $message
     * @return array
     */
    public function sendMessage(string $phone, string $message): array
    {
        $phone = $this->formatPhoneNumber($phone);
        
        $url = $this->baseUrl . '/send-message';

        try {
            // First attempt with Bearer (most standard for JWT)
            $payload = [
                'receiver' => $phone,
                'message' => $message,
                'media_type' => 'text',
                'inbox_id' => env('CEKAT_INBOX_ID'), // Added Inbox ID
            ];

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withToken($this->apiKey)->post($url, $payload);

            // Fallback 1: Raw Authorization header
            if ($response->status() === 401) {
                $response = Http::withHeaders(['Authorization' => $this->apiKey])->post($url, $payload);
            }

            // Fallback 2: x-api-key header (common in many APIs)
            if ($response->status() === 401) {
                $response = Http::withHeaders(['x-api-key' => $this->apiKey])->post($url, $payload);
            }

            // Fallback 3: api-key header (dashed)
            if ($response->status() === 401) {
                 $response = Http::withHeaders(['api-key' => $this->apiKey])->post($url, $payload);
            }

            if ($response->successful()) {
                Log::info("[CekatService] Message sent to $phone");
                return ['success' => true, 'data' => $response->json()];
            } else {
                Log::error("[CekatService] Failed: " . $response->body());
                return [
                    'success' => false,
                    'error' => $response->json() ?? $response->body(),
                    'debug_info' => [
                        'endpoint' => $url,
                        'payload' => $payload,
                        'status' => $response->status()
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error("[CekatService] Exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a WhatsApp Template message.
     *
     * @param string $phone
     * @param string $templateName
     * @param array $parameters
     * @return array
     */
    /**
     * Send a WhatsApp Template message.
     *
     * @param string $phone
     * @param string $templateId (wa_template_id) - passed as 2nd arg
     * @param array $parameters (template_body_variables)
     * @return array
     */
    public function sendTemplate(string $phone, string $templateId, array $parameters = []): array
    {
        $phone = $this->formatPhoneNumber($phone);
        
        // Correct Endpoint from Docs
        $url = 'https://api.cekat.ai/templates/send';
        
        // Get Inbox ID from Env (Required)
        $inboxId = env('CEKAT_INBOX_ID');
        
        if (empty($inboxId)) {
             return ['success' => false, 'error' => 'CEKAT_INBOX_ID belum disetting di .env'];
        }

        $payload = [
            'phone_number' => $phone,
            'wa_template_id' => $templateId, // Template ID (Number string), not Name
            'inbox_id' => $inboxId,
            'template_body_variables' => $parameters
            // 'phone_name' => 'Name', // Optional
        ];

        // Headers
        // Reverting to Bearer as it's more standard for JWT
        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ];

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders($headers)->post($url, $payload);

            if ($response->successful()) {
                Log::info("[CekatService] Template ID '$templateId' sent to $phone");
                return ['success' => true, 'data' => $response->json()];
            } else {
                Log::error("[CekatService] Failed: " . $response->body());
                return [
                    'success' => false, 
                    'error' => $response->json() ?? $response->body(),
                    'debug_info' => [
                         'endpoint' => $url,
                         'payload' => $payload,
                         'status' => $response->status()
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error("[CekatService] Exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function formatPhoneNumber(string $phone): string
    {
        // Remove non-numeric
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
}
