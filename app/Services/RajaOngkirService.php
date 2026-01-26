<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RajaOngkirService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        // Starter URL (Default)
        // $this->baseUrl = 'https://api.rajaongkir.com/starter'; 
        
        // Error 410 on Starter suggests Account Type Mismatch or Invalid Endpoint for this Key.
        // Trying BASIC endpoint (common upgrade path)
        $this->baseUrl = 'https://api.rajaongkir.com/basic'; 

        // Fallback to provided key if env missing
        $this->apiKey = config('services.rajaongkir.key', env('RAJAONGKIR_API_KEY', '5ccc34dded94c0be15b30fd9168a87be'));
    }

    /**
     * Search City by Name
     * @param string $query
     * @return array
     */
    public function searchCities($query)
    {
        // 1. Fetch Data Directly (No Cache for Debug)
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withoutVerifying()->withHeaders(['key' => $this->apiKey])->get($this->baseUrl . '/city');
            
            if ($response->successful()) {
                $rawCities = $response->json()['rajaongkir']['results'] ?? [];
            } else {
                // API ERROR RESPONSE
                return [[
                    'id' => '0',
                    'text' => 'API ERROR: ' . $response->status() . ' - ' . substr($response->body(), 0, 30),
                    'postal_code' => 'ERR'
                ]];
            }
        } catch (\Exception $e) {
            // CONNECTION ERROR
            return [[
                'id' => '0',
                'text' => 'CONN ERROR: ' . substr($e->getMessage(), 0, 40),
                'postal_code' => 'ERR'
            ]];
        }

        // 2. Filter Results
        $results = [];
        foreach ($rawCities as $city) {
            $cityName = $city['type'] . ' ' . $city['city_name'] . ', ' . $city['province'];
            if (stripos($cityName, $query) !== false) {
                $results[] = [
                    'id' => $city['city_id'],
                    'text' => $cityName,
                    'postal_code' => $city['postal_code']
                ];
                if (count($results) >= 10) break; // Limit results
            }
        }

        return $results;
    }

    /**
     * Get Shipping Cost
     * @param int $destinationId
     * @param int $weight (grams)
     * @param string $courier (jne, pos, tiki)
     */
    public function getCost($destinationId, $weight = 1000, $courier = 'jne')
    {
        // Origin: Bandung (ID 23) - Hardcoded for now based on Workshop location
        // User provided info: "Kec. Cidadap, Kota Bandung".
        // RajaOngkir Starter City ID for Bandung is 23.
        $origin = 23; 

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withoutVerifying()->withHeaders(['key' => $this->apiKey])
                        ->post($this->baseUrl . '/cost', [
                            'origin' => $origin,
                            'destination' => $destinationId,
                            'weight' => $weight,
                            'courier' => $courier
                        ]);

        if ($response->successful()) {
            return $response->json()['rajaongkir']['results'][0]['costs'] ?? [];
        }
        
        Log::error('RajaOngkir Error: ' . $response->body());
        return [];
    }
}
