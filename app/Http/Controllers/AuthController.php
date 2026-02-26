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
        $this->middleware('auth:api', ['except' => ['auth', 'register', 'webLogin', 'webLogout', 'loginView', 'webVerify2fa']]);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Регистрация пользователя",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Иван Иванов"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", example="secret123"),
     *             @OA\Property(property="role", type="string", enum={"user","admin","finance","redactor","manager"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Пользователь зарегистрирован"),
     *     @OA\Response(response=422, description="Ошибка валидации")
     * )
     */
    public function register(Request $request): \Illuminate\Http\JsonResponse
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
     * @OA\Post(
     *     path="/api/auth",
     *     summary="Вход пользователя (получение JWT токена)",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200, description="Успешный вход",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Неверные учётные данные")
     * )
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['message' => 'Неверные учетные данные'], 401);
        }

        return $this->respondWithToken((string)$token);
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Получить текущего пользователя",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Данные пользователя"),
     *     @OA\Response(response=401, description="Не авторизован")
     * )
     */
    public function me(): \Illuminate\Http\JsonResponse
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Выход пользователя",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Успешный выход"),
     *     @OA\Response(response=401, description="Не авторизован")
     * )
     */
    public function logout(): \Illuminate\Http\JsonResponse
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Успешный выход']);
    }

    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     summary="Обновить JWT токен",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200, description="Новый токен",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Не авторизован")
     * )
     */
    public function refresh(): \Illuminate\Http\JsonResponse
    {
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = Auth::guard('api');
        return $this->respondWithToken($guard->refresh());
    }

    /**
     * Вернуть JSON ответ с токеном
     */
    protected function respondWithToken(string $token): \Illuminate\Http\JsonResponse
    {
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = Auth::guard('api');
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
            'user' => $guard->user(),
        ]);
    }

    /**
     * Показать форму входа (веб)
     */
    /**
     * @OA\Get(
     *     path="/auth",
     *     summary="Display login page",
     *     @OA\Response(response=200, description="Login page")
     * )
     */
    public function loginView(): \Illuminate\View\View
    {
        return view('auth.auth');
    }

    /**
     * Вход пользователя (веб)
     */
    public function webLogin(Request $request): \Symfony\Component\HttpFoundation\Response
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

        if ($user && Cache::has('user_session_' . $user->id) && Cache::get('user_session_' . $user->id) !== $request->session()->getId()) {
            return back()->withErrors([
                'email' => 'Пользователь уже находится в системе с другого устройства.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            Cache::put('user_session_' . Auth::id(), $request->session()->getId(), now()->addMinutes(120));

            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($remember) {
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
            if ($user->two_factor_confirmed_at) {
                // Если 2FA включена, перенаправляем на проверку кода
                session()->put('2fa_verified', false);
                return redirect()->route('2fa.verify');
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
    public function webLogout(Request $request): \Illuminate\Http\RedirectResponse
    {
        if (Auth::check()) {
            \Log::info("User logout, clearing online status", ['user_id' => Auth::id()]);
            Cache::forget('user_session_' . Auth::id());
        }

        Auth::logout();
        $request->session()->forget('2fa_verified');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/auth');
    }

    /**
     * Проверка 2FA кода (веб)
     */
    public function webVerify2fa(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'one_time_password' => 'required',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var \PragmaRX\Google2FA\Google2FA $google2fa */
        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey((string)$user->google2fa_secret, (string)$request->one_time_password);

        if ($valid) {
            $request->session()->put('2fa_verified', true);
            return redirect('/users');
        }

        return back()->withErrors(['one_time_password' => 'Неверный код. Попробуйте еще раз.']);
    }
}
