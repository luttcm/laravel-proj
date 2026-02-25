<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TrackUserOnline
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $storedSessionId = Cache::get('user_session_' . $userId);

            if ($storedSessionId && $storedSessionId !== $request->session()->getId()) {
                \Log::info("Session mismatch detected, logging out user", [
                    'user_id' => $userId,
                    'stored_session' => $storedSessionId,
                    'current_session' => $request->session()->getId()
                ]);

                Auth::logout();
                Cache::forget('user_session_' . $userId);
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('auth')->withErrors([
                    'email' => 'Вы вышли из системы, так как выполнен вход с другого устройства.',
                ]);
            }

            Cache::put('user_session_' . $userId, $request->session()->getId(), now()->addMinutes(10));
        }

        return $next($request);
    }
}
