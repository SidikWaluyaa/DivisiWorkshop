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
        // Using the JWT provided by user as the key.
        $this->apiKey = env('CEKAT_API_KEY', '');
        // Default base URL, can be overridden in env
        $this->baseUrl = env('CEKAT_BASE_URL', 'https://chat.cekat.ai/api'); 
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

        // Based on typical Cekat/WatZap structures. We might need to adjust endpoint if it differs.
        // Assuming standard /v1/message/text or similar for BSPs.
        // However, since we don't have exact docs, we will use a generic endpoint structure often seen.
        // We actually found no docs for "chat.cekat.ai" specific endpoints in search, 
        // will try a standard structure and log the response for debugging.
        
        // Official Endpoint from Postman Docs: POST /messages/whatsapp
        $url = $this->baseUrl . '/messages/whatsapp';

        try {
            // Official Auth: Bearer Token
            $response = Http::withToken($this->apiKey)
                ->post($url, [
                    'receiver' => $phone, // Doc uses "receiver" not "to"
                    'message' => $message,
                    'media_type' => 'text'
                ]);

            if ($response->successful()) {
                Log::info("[CekatService] Message sent to $phone");
                return ['success' => true, 'data' => $response->json()];
            } else {
                Log::error("[CekatService] Failed: " . $response->body());
                return ['success' => false, 'error' => $response->body()];
            }
        } catch (\Exception $e) {
            Log::error("[CekatService] Exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function formatPhoneNumber(string $phone): string
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
