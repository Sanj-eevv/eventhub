<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexController::class)->name('index');

Route::middleware('guest')->group(function (): void {
    Route::as('auth.')->group(function (): void {
        Route::get('register', [RegisterController::class, 'register'])->name('register');
        Route::post('register', [RegisterController::class, 'store'])->name('register.store');
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');
    });

});

Route::middleware('auth')->group(function (): void {
    Route::as('auth.')->group(function (): void {
        Route::post('logout', LogoutController::class)->name('logout');
    });
    Route::as('dashboard.')->group(function (): void {
        Route::get('dashboard', DashboardController::class)->name('index');
    })->middleware('verified');
});
