<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;


Route::post('/login', [AuthController::class, 'login'])
    ->name('api.login');

Route::post('/users/company', [UserController::class, 'createCompanyUser'])
->name('api.createCompanyUser');

Route::post('/users/provider', [UserController::class, 'createProviderUser'])
    ->name('api.createProviderUser');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.logout');

    Route::get('/users', [UserController::class, 'index'])
        ->name('api.getUser');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('api.getUserByIdentifier');

    Route::post('/users/admin', [UserController::class, 'createAdminUser'])
        ->name('api.createAdminUser');

    Route::patch('/users/{user}', [UserController::class, 'update'])
        ->name('api.updateUser');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('api.deleteUser');
    });
