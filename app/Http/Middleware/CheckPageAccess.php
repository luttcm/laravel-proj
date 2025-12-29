<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPageAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('news.index');
        }

        $path = $request->path();
        $userRole = $user->role;

        if (strpos($path, 'users') === 0) {
            if (!in_array($userRole, ['admin', 'manager'])) {
                return redirect()->route('news.index')->with('error', 'У вас нет прав доступа к этой странице');
            }
        }

        if (strpos($path, 'news/create') === 0) {
            if (!in_array($userRole, ['admin', 'redactor'])) {
                return redirect()->route('news.index')->with('error', 'У вас нет прав доступа к этой странице');
            }
        }

        if (preg_match('#^news/\d+/edit$#', $path)) {
            if (!in_array($userRole, ['admin', 'redactor'])) {
                return redirect()->route('news.index')->with('error', 'У вас нет прав доступа к этой странице');
            }
        }

                if (strpos($path, 'variables') === 0) {
            if (!in_array($userRole, ['admin', 'manager'])) {
                return redirect()->route('news.index')->with('error', 'У вас нет прав доступа к этой странице');
            }
        }

        return $next($request);
    }
}
