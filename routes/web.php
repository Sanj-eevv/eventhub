<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexController::class)->name('index');
Route::middleware('guest')->as('auth.')->group(function (): void {
    Route::get('register', [RegisterController::class, 'register'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');
});
