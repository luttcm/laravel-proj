<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::get('/auth', function () {
    return view('auth.auth');
})->name('auth');

Route::post('/auth', [AuthController::class, 'webLogin'])->name('auth.post');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');

    Route::middleware('role:admin,manager')->group(function () {
        Route::post('/users/delete', [UserController::class, 'delete'])->name('users.delete');
        Route::get('/users/add', [UserController::class, 'add'])->name('user.add');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    });

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/avatar', [UserController::class, 'updateAvatar'])->name('profile.avatar');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');

    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
});
