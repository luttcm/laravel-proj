<?php

use App\Http\Controllers\NewsController;
use App\Http\Controllers\VariableController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagersController;

Route::get('/auth', function () {
    return view('auth.auth');
})->name('auth');

Route::post('/auth', [AuthController::class, 'webLogin'])->name('auth.post');

Route::middleware(['auth', 'check.access'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('news.index');
    })->name('home');

    Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');

    //Пользователи
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show')->whereNumber('id');

    Route::middleware('role:admin,manager')->group(function () {
        Route::post('/users/delete', [UserController::class, 'delete'])->name('users.delete');
        Route::get('/users/add', [UserController::class, 'add'])->name('user.add');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

        //Переменные
        Route::get('/variables', [VariableController::class, 'index'])->name('variables.index');
        Route::get('/variables/add', [VariableController::class, 'add'])->name('variable.add');
        Route::post('/variables', [VariableController::class, 'store'])->name('variables.store');
        Route::get('/variables/{id}/edit', [VariableController::class, 'edit'])->name('variable.edit')->whereNumber('id');
        Route::put('/variables/{id}', [VariableController::class, 'update'])->name('variable.update')->whereNumber('id');
        Route::post('/variables/{id}/delete', [VariableController::class, 'delete'])->name('variable.delete')->whereNumber('id');
    });

    // Профиль
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/avatar', [UserController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

    // Новости
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show')->whereNumber('id');
    Route::post('/news/{id}/like', [NewsController::class, 'toggleLike'])->name('news.like')->whereNumber('id');
    Route::post('/news/{newsId}/comments', [NewsController::class, 'storeComment'])->name('news.comments.store')->whereNumber('newsId');
    Route::delete('/news/{newsId}/comments/{commentId}', [NewsController::class, 'deleteComment'])->name('news.comments.delete')->whereNumber('newsId')->whereNumber('commentId');

    Route::middleware('role:admin,redactor')->group(function () {
        Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
        Route::post('/news', [NewsController::class, 'store'])->name('news.store');
        Route::get('/news/{id}/edit', [NewsController::class, 'edit'])->name('news.edit')->whereNumber('id');
        Route::put('/news/{id}', [NewsController::class, 'update'])->name('news.update')->whereNumber('id');
        Route::delete('/news/{id}', [NewsController::class, 'destroy'])->name('news.destroy')->whereNumber('id');
        Route::delete('/pictures/{pictureId}', [NewsController::class, 'deletePicture'])->name('pictures.delete')->whereNumber('pictureId');
    });

    // Страница менеджеров
    Route::get('/managers', [ManagersController::class, 'calculation'])->name('managers.calculation');
    Route::get('/managers/reports', [ManagersController::class, 'reports'])->name('managers.reports');
    Route::get('/managers/history', [ManagersController::class, 'history'])->name('managers.history');
    Route::get('/managers/variables', [ManagersController::class, 'getVariables'])->name('managers.get-variables');
    Route::post('/managers/store-drafts-report', [ManagersController::class, 'storeDraftsReport'])->name('managers.store-drafts-report');
    Route::post('/managers/store-report', [ManagersController::class, 'storeReport'])->name('managers.store-report');

    // Страница финансового директора
    Route::get('/findirector', function () {
        return view('pages.findirector');
    })->name('findirector');
});
