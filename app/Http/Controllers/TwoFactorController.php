<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Показать страницу настройки 2FA
     */
    /**
     * Показать страницу настройки 2FA
     */
    public function setup(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            (string)$user->google2fa_secret
        );

        $google2faUrl = app('pragmarx.google2fa')->getQRCodeInline(
            config('app.name'),
            $user->email,
            (string)$user->google2fa_secret
        );

        return view('auth.2fa_setup', [
            'user' => $user,
            'qr_code_url' => $google2faUrl,
            'secret' => $user->google2fa_secret,
        ]);
    }

    /**
     * Подтвердить настройку 2FA
     */
    /**
     * Подтвердить настройку 2FA
     */
    public function confirm(Request $request): \Illuminate\Http\RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        $secret = (string)$request->input('code');
        $valid = $google2fa->verifyKey((string)$user->google2fa_secret, $secret);

        if ($valid) {
            $user->two_factor_confirmed_at = now();
            $user->save();

            session()->put('2fa_verified', true);

            return redirect()->route('profile')->with('success', '2FA успешно включена.');
        }

        return back()->withErrors(['code' => 'Неверный код. Попробуйте еще раз.']);
    }

    /**
     * Отключить 2FA
     */
    /**
     * Отключить 2FA
     */
    public function disable(): \Illuminate\Http\RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->google2fa_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return redirect()->route('profile')->with('success', '2FA успешно отключена.');
    }
}
