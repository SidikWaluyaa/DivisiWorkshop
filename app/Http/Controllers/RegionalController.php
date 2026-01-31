<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RegionalController extends Controller
{
    const API_BASE = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    public function provinces()
    {
        return Cache::remember('regional_provinces', 86400, function () {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withoutVerifying()->get(self::API_BASE . '/provinces.json');
            return $response->successful() ? $response->json() : [];
        });
    }

    public function regencies($provinceId)
    {
        return Cache::remember("regional_regencies_{$provinceId}", 86400, function () use ($provinceId) {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withoutVerifying()->get(self::API_BASE . "/regencies/{$provinceId}.json");
            return $response->successful() ? $response->json() : [];
        });
    }

    public function districts($regencyId)
    {
        return Cache::remember("regional_districts_{$regencyId}", 86400, function () use ($regencyId) {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withoutVerifying()->get(self::API_BASE . "/districts/{$regencyId}.json");
            return $response->successful() ? $response->json() : [];
        });
    }

    public function villages($districtId)
    {
        return Cache::remember("regional_villages_{$districtId}", 86400, function () use ($districtId) {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withoutVerifying()->get(self::API_BASE . "/villages/{$districtId}.json");
            return $response->successful() ? $response->json() : [];
        });
    }
}
