<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('home');
})->name('home');

// Маршруты аутентификации для веб (доступны только неавторизованным пользователям)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginView'])->name('login');
    Route::post('/login', [AuthController::class, 'webLogin']);
});

// Выход (только для авторизованных)
Route::post('/logout', [AuthController::class, 'webLogout'])->middleware('auth')->name('logout');

// Маршруты для пользователей
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
