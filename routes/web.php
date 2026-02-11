<?php

use App\Http\Controllers\NdsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\VariableController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\SupplierController;
use App\Models\Nds;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagersController;
use App\Http\Controllers\FinDirectorController;

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

    Route::middleware('role:admin,manager,finance')->group(function () {
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

        Route::get('/nds', [NdsController::class, 'index'])->name('nds.index');
        Route::get('/nds/add', [NdsController::class, 'add'])->name('nds.add');
        Route::post('/nds', [NdsController::class, 'store'])->name('nds.store');
        Route::get('/nds/{id}/edit', [NdsController::class, 'edit'])->name('nds.edit')->whereNumber('id');
        Route::put('/nds/{id}', [NdsController::class, 'update'])->name('nds.update')->whereNumber('id');
        Route::post('/nds/{id}/delete', [NdsController::class, 'delete'])->name('nds.delete')->whereNumber('id');

        // СПК
        Route::get('/spk', [SpkController::class, 'index'])->name('spk.index');
        Route::get('/spk/add', [SpkController::class, 'add'])->name('spk.add');
        Route::post('/spk', [SpkController::class, 'store'])->name('spk.store');
        Route::get('/spk/{id}/edit', [SpkController::class, 'edit'])->name('spk.edit')->whereNumber('id');
        Route::put('/spk/{id}', [SpkController::class, 'update'])->name('spk.update')->whereNumber('id');
        Route::post('/spk/{id}/delete', [SpkController::class, 'delete'])->name('spk.delete')->whereNumber('id');

        // Поставщики
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('supplier.index');
        Route::get('/suppliers/add', [SupplierController::class, 'add'])->name('supplier.add');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('supplier.store');
        Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit')->whereNumber('id');
        Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('supplier.update')->whereNumber('id');
        Route::post('/suppliers/{id}/delete', [SupplierController::class, 'delete'])->name('supplier.delete')->whereNumber('id');
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
    Route::get('/managers/reports/{id}', [ManagersController::class, 'getReport'])->name('managers.get-report')->whereNumber('id');
    Route::get('/managers/history', [ManagersController::class, 'history'])->name('managers.history');
    Route::get('/managers/variables', [ManagersController::class, 'getVariables'])->name('managers.get-variables');
    Route::get('/managers/nds', [ManagersController::class, 'getNds'])->name('managers.get-nds');
    Route::post('/managers/store-drafts-report', [ManagersController::class, 'storeDraftsReport'])->name('managers.store-drafts-report');
    Route::post('/managers/store-report', [ManagersController::class, 'storeReport'])->name('managers.store-report');
    Route::post('/managers/calculate', [ManagersController::class, 'calculate'])->name('managers.calculate');

    // Страница финансового директора
    Route::middleware('role:admin,finance')->group(function () {
        Route::get('/findirector', [FinDirectorController::class, 'reports'])->name('findirector');
        Route::get('/findirector/calculation', [FinDirectorController::class, 'calculation'])->name('findirector.calculation');
        Route::get('/findirector/reports', [FinDirectorController::class, 'reports'])->name('findirector.reports');
        Route::get('/findirector/reports/{id}', [FinDirectorController::class, 'getReport'])->name('findirector.get-report')->whereNumber('id');
        Route::get('/findirector/history', [FinDirectorController::class, 'history'])->name('findirector.history');
        
        // Ручные отчеты (как переменные)
        Route::get('/findirector/fin-reports', [FinDirectorController::class, 'finReportsIndex'])->name('findirector.fin-reports.index');
        Route::get('/findirector/fin-reports/add', [FinDirectorController::class, 'finReportsAdd'])->name('findirector.fin-reports.add');
        Route::post('/findirector/fin-reports', [FinDirectorController::class, 'finReportsStore'])->name('findirector.fin-reports.store');
        Route::get('/findirector/fin-reports/{id}/edit', [FinDirectorController::class, 'finReportsEdit'])->name('findirector.fin-reports.edit')->whereNumber('id');
        Route::put('/findirector/fin-reports/{id}', [FinDirectorController::class, 'finReportsUpdate'])->name('findirector.fin-reports.update')->whereNumber('id');
        Route::post('/findirector/fin-reports/{id}/delete', [FinDirectorController::class, 'finReportsDelete'])->name('findirector.fin-reports.delete')->whereNumber('id');

        Route::get('/findirector/variables', [FinDirectorController::class, 'getVariables'])->name('findirector.get-variables');
        Route::get('/findirector/nds', [FinDirectorController::class, 'getNds'])->name('findirector.get-nds');
        Route::post('/findirector/store-drafts-report', [FinDirectorController::class, 'storeDraftsReport'])->name('findirector.store-drafts-report');
        Route::post('/findirector/store-report', [FinDirectorController::class, 'storeReport'])->name('findirector.store-report');
        Route::post('/findirector/calculate', [FinDirectorController::class, 'calculate'])->name('findirector.calculate');
    });
});
