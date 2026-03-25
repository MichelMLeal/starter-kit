<?php

use App\Application\Auth\Controllers\LoginController;
use App\Application\Auth\Controllers\LogoutController;
use App\Application\Auth\Controllers\RegisterController;
use App\Application\Auth\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', RegisterController::class)->name('auth.register');
    Route::post('/login', LoginController::class)->name('auth.login');
    Route::post('/refresh', [TokenController::class, 'refresh'])->name('auth.refresh');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [LogoutController::class, 'logout'])->name('auth.logout');
        Route::get('/me', [LogoutController::class, 'me'])->name('auth.me');
    });
});
