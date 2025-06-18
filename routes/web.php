<?php

use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\StaffAdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::name("admin.")->prefix("admin")->group(function () {
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register.submit');

    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/account-profile', [AuthController::class, 'accountProfile'])->name('profile');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [AuthController::class, 'dashboard'])
        ->middleware('auth')
        ->name('dashboard');
    Route::get('dashboard', [DashboardAdminController::class, 'index'])->middleware('auth')->name('dashboard');

    Route::name("product.")->prefix("product")->group(function () {
        Route::get('/', [ProductAdminController::class, 'index'])->name('index');
        Route::post('/', [ProductAdminController::class, 'store'])->name('store');
        Route::get('/{id}', [ProductAdminController::class, 'show'])->name('show');
        Route::patch('/{id}', [ProductAdminController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProductAdminController::class, 'destroy'])->name('destroy');
    });

    Route::name("staff.")->prefix("staff")->group(function () {
        Route::get("/", [StaffAdminController::class, "index"])->name("index");
        // Route::post("/")->name("store");
        Route::get("{id}", [StaffAdminController::class, "show"])->name("show");
        // Route::patch("/")->name("update");
        // Route::delete("/")->name("delete");
    });
});
