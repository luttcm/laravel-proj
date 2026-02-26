<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * @param string $token
     * @return array<string, mixed>
     */
    public function getRespondWithToken(string $token)
    {
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = Auth::guard('api');
        
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
            'user' => $guard->user(),
        ];
    }

    /**
     * @param array<string, mixed> $credentials
     * @param bool $remember
     * @param Request $request
     * @return array{success: bool, redirect?: string, errors?: array<string, string>}
     */
    public function webLogin(array $credentials, bool $remember, Request $request): array
    {
        $rememberMinutes = (int) env('REMEMBER_ME_MINUTES', 43200);
        $user = User::where('email', $credentials['email'])->first();

        \Log::info("Login attempt for {$credentials['email']}", [
            'user_found' => (bool)$user,
            'is_online' => $user ? Cache::has('user_session_' . $user->id) : false,
            'cache_key' => $user ? 'user_session_' . $user->id : null
        ]);

        if ($user && Cache::has('user_session_' . $user->id) && Cache::get('user_session_' . $user->id) !== $request->session()->getId()) {
            return [
                'success' => false,
                'errors' => ['email' => 'Пользователь уже находится в системе с другого устройства.']
            ];
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            Cache::put('user_session_' . Auth::id(), $request->session()->getId(), now()->addMinutes(120));

            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($remember) {
                $this->handleRememberMe($user, $rememberMinutes);
            }

            if ($user->two_factor_confirmed_at) {
                session()->put('2fa_verified', false);
                return ['success' => true, 'redirect' => route('2fa.verify')];
            }

            return ['success' => true, 'redirect' => '/users'];
        }

        return [
            'success' => false,
            'errors' => ['email' => 'Неправильные введенные логин или пароль.']
        ];
    }

    /**
     * @param string $oneTimePassword
     * @return bool
     */
    public function verify2fa(string $oneTimePassword): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var \PragmaRX\Google2FA\Google2FA $google2fa */
        $google2fa = app('pragmarx.google2fa');

        return (bool)$google2fa->verifyKey((string)$user->google2fa_secret, $oneTimePassword);
    }

    protected function handleRememberMe(User $user, int $rememberMinutes): void
    {
        $token = $user->getRememberToken();
        if (!$token) {
            $user->setRememberToken(Str::random(60));
            $user->save();
            $token = $user->getRememberToken();
        }
        
        /** @var string $name */
        $name = Auth::getRecallerName();
        if (empty($name)) {
            $name = 'remember_web';
        }
        
        cookie()->queue(cookie(
            $name,
            $user->getAuthIdentifier().'|'.$token.'|'.$user->password,
            $rememberMinutes
        ));
    }
}
