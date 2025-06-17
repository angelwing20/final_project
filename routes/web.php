<?php

use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\StaffAdminController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::name("admin.")->prefix("admin")->group(function () {

    Route::get("/", [DashboardAdminController::class, "index"])->name("dashboard");

    Route::name("staff.")->prefix("staff")->group(function () {
        Route::get("/", [StaffAdminController::class, "index"])->name("index");
        Route::get("{id}", [StaffAdminController::class, "show"])->name("show");
    });

       Route::name("product.")->prefix("product")->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/search', [ProductController::class, 'search'])->name('search');
            Route::get('/{id}', [ProductController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::patch('/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
    });

});
