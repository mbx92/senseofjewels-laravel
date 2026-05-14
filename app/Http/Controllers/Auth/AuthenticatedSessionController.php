<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        protected CartService $cartService,
    ) {}

    /**
     * Display the login view.
     */
    public function create(Request $request): Response
    {
        if ($request->routeIs('admin.*')) {
            return Inertia::render('Auth/AdminLogin');
        }

        return Inertia::render('Auth/Login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $previousSessionId = $request->session()->getId();
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->routeIs('admin.*') && ! $request->user()->hasAnyRole(['super-admin', 'admin'])) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Your account does not have admin access.',
            ]);
        }

        if (! $request->routeIs('admin.*')) {
            $this->cartService->merge(
                userId: (int) $request->user()->id,
                fromSessionId: $previousSessionId,
                toSessionId: $request->session()->getId(),
            );
        }

        $route = $request->routeIs('admin.*') ? 'admin.dashboard' : 'dashboard';

        return redirect()->intended(route($route, absolute: false));
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
