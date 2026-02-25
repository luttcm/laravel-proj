<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->two_factor_confirmed_at) {
            if (!session('2fa_verified')) {
                if (!$request->is('2fa/verify*') && !$request->routeIs('logout')) {
                    \Log::info("2FA check triggered: user {$user->id} redirected to verify from {$request->path()}");
                    return redirect()->route('2fa.verify');
                }
            }
        }

        return $next($request);
    }
}
