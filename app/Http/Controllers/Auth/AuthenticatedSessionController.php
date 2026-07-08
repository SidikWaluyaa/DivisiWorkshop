<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Helpers\ActivityLogger;

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

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && !$user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('deactivated', true);
        }

        $request->session()->regenerate();

        ActivityLogger::log('Login ke sistem', 'User ' . $user->name . ' (' . $user->email . ') berhasil masuk ke dalam sistem.');

        // PRIORITY REDIRECTION based on Role
        $roleHomeMap = [
            'admin'      => 'dashboard',
            'owner'      => 'dashboard',
            'spv'        => 'workshop.dashboard',
            'technician' => 'workshop.dashboard',
            'gudang'     => 'reception.index',
            'cs'         => 'cs.dashboard',
            'finance'    => 'finance.index',
            'pic'        => 'sortir.index',
            'hr'         => 'admin.users.index',
        ];

        if (isset($roleHomeMap[$user->role])) {
            $routeName = $roleHomeMap[$user->role];
            // Verify access before redirecting to the role's "home"
            // Special case for dashboard which uses 'access:dashboard'
            $moduleKey = $routeName === 'dashboard' ? 'dashboard' : $routeName;
            
            // Map common route prefixes to module keys if needed
            $moduleKey = str_replace('.index', '', $moduleKey);
            $moduleKey = str_replace('.dashboard', '.dashboard', $moduleKey); // redundant but for clarity

            if ($user->hasAccess($moduleKey)) {
                return redirect()->intended(route($routeName, absolute: false));
            }
        }

        // SECONDARY REDIRECTION based on assigned rights (First one found)
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

        foreach ($redirectionMap as $module => $routeName) {
            if ($user->hasAccess($module)) {
                return redirect()->intended(route($routeName, absolute: false));
            }
        }

        // Default fallback to Profile (safe for all roles)
        return redirect()->intended(route('profile.edit', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            ActivityLogger::log('Logout dari sistem', 'User ' . Auth::user()->name . ' keluar dari sistem.');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
