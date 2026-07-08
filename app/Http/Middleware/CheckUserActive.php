<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('deactivated', true);
            }

            // Update last_active_at (Throttled to once every 60 seconds to save DB writes)
            if (!$user->last_active_at || now()->diffInSeconds($user->last_active_at) >= 60) {
                $user->timestamps = false; // Disable updated_at update
                $user->update(['last_active_at' => now()]);
            }
        }

        return $next($request);
    }
}
