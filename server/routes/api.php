<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Project\CategoryController;
use App\Http\Controllers\Project\ProblemController;


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
        ->name('api.getUsers');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('api.getUser');

    Route::post('/users/admin', [UserController::class, 'createAdminUser'])
        ->name('api.createAdminUser');

    Route::patch('/users/{user}', [UserController::class, 'update'])
        ->name('api.updateUser');

    Route::patch('/users/{user}/portfolio-links', [UserController::class, 'updatePortfolioLinks'])
        ->name('api.updatePortfolioLinks');

    Route::patch('/users/{user}/categories', [UserController::class, 'updateUserCategory'])
        ->name('api.updateUserCategory');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('api.deleteUser');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('api.getCategories');

    Route::get('/categories/{category}', [CategoryController::class, 'show'])
        ->name('api.getCategory');

    Route::post('/categories', [CategoryController::class, 'create'])
        ->name('api.createCategory');

    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->name('api.deleteCategory');

    Route::post('/problems', [ProblemController::class, 'create'])
        ->name('api.createProblem');
    });
