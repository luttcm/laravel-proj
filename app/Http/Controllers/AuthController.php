<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['auth', 'register', 'webLogin', 'webLogout', 'loginView']]);
    }

    /**
     * Регистрация нового пользователя
     * @param Request $request
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:user,admin,finance,redactor,manager',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'created_at' => now(),
            'updated_at' => now(),
            'role' => $validated['role'] ?? 'user',
        ]);

        return response()->json([
            'message' => 'Пользователь успешно зарегистрирован',
            'user' => $user,
        ], 201);
    }

    /**
     * Вход пользователя
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['message' => 'Неверные учетные данные'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Получить текущего пользователя
     */
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Выход пользователя
     */
    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Успешный выход']);
    }

    /**
     * Обновить токен
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    /**
     * Вернуть JSON ответ с токеном
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user(),
        ]);
    }

    /**
     * Показать форму входа (веб)
     */
    public function loginView()
    {
        return view('auth.auth');
    }

    /**
     * Вход пользователя (веб)
     */
    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');
        $rememberMinutes = (int) env('REMEMBER_ME_MINUTES', 43200);

        $user = User::where('email', $credentials['email'])->first();

        \Log::info("Login attempt for {$credentials['email']}", [
            'user_found' => (bool)$user,
            'is_online' => $user ? Cache::has('user_session_' . $user->id) : false,
            'cache_key' => $user ? 'user_session_' . $user->id : null
        ]);

        if ($user && Cache::has('user_session_' . $user->id)) {
            return back()->withErrors([
                'email' => 'Пользователь уже находится в системе с другого устройства.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            Cache::put('user_session_' . Auth::id(), $request->session()->getId(), now()->addMinutes(2));

            if ($remember) {
                $user = Auth::user();
                $token = $user->getRememberToken();
                if (!$token) {
                    $user->setRememberToken(Str::random(60));
                    $user->save();
                    $token = $user->getRememberToken();
                }
                cookie()->queue(cookie(
                    Auth::getRecallerName(),
                    $user->getAuthIdentifier().'|'.$token.'|'.$user->password,
                    $rememberMinutes
                ));
            }
            return redirect('/users');
        }

        return back()->withErrors([
            'email' => 'Неправильные введенные логин или пароль.',
        ])->onlyInput('email');
    }

    /**
     * Выход пользователя (веб)
     */
    public function webLogout(Request $request)
    {
        if (Auth::check()) {
            \Log::info("User logout, clearing online status", ['user_id' => Auth::id()]);
            Cache::forget('user_session_' . Auth::id());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/auth');
    }
}
