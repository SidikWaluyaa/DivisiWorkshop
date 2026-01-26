<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KiriminAjaService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.kiriminaja.base_url', 'https://api.kiriminaja.com/api/home');
        $this->apiKey = config('services.kiriminaja.api_key');
    }

    /**
     * Get Shipping Rates
     * @param string $originCityId
     * @param string $destinationCityId
     * @param int $weight (grams)
     * @param string $itemType (default: 'paket')
     */
    public function getRates($originId, $destinationId, $weight = 1000)
    {
        try {
            // Placeholder: Adjust according to official KiriminAja API payload
            // Usually returns a list of services (JNE, J&T, etc)
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->baseUrl . '/v2/shipping_rates', [
                'origin' => $originId,
                'destination' => $destinationId,
                'weight' => $weight,
            ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            Log::error('KiriminAja API Error: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('KiriminAja Exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search For Location IDs
     * KiriminAja usually requires internal IDs for districts/cities
     */
    public function searchLocation($query)
    {
        // Placeholder for location search
        return [];
    }
}
