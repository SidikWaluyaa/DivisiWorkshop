<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $expectedKey = env('KPI_API_KEY', 'default-secret-key-123'); // Nanti Anda ubah di .env
        $providedKey = $request->bearerToken() ?? $request->header('x-api-key') ?? $request->query('api_key');

        if (!$providedKey || $providedKey !== $expectedKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid API Key.',
            ], 401);
        }

        return $next($request);
    }
}
