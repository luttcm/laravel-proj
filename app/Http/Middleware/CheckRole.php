<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Проверка роли пользователя.
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'У вас нет доступа к этому ресурсу.');
        }

        return $next($request);
    }
}
