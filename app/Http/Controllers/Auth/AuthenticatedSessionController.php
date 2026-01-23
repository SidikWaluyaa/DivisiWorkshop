<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Admin always goes to dashboard
        if ($user->role === 'admin') {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Define mapping of module keys to routes
        // Priority order for redirection
        $redirectionMap = [
            'dashboard'          => 'dashboard',
            'workshop.dashboard' => 'workshop.dashboard',
            'admin.performance'  => 'admin.performance.index',
            'gudang'             => 'reception.index',
            'finance'            => 'finance.index',
            'assessment'         => 'assessment.index',
            'preparation'        => 'preparation.index',
            'sortir'             => 'sortir.index',
            'production'         => 'production.index',
            'qc'                 => 'qc.index',
            'finish'             => 'finish.index',
            'cx'                 => 'cx.index',
            'cs'                 => 'cs.dashboard',
        ];

        // Find the first module user has access to
        foreach ($redirectionMap as $module => $routeName) {
            if ($user->hasAccess($module)) {
                return redirect()->intended(route($routeName, absolute: false));
            }
        }

        // Default fallback (handled by middleware if restricted)
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
