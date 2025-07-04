<?php

use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\ProductCategoryAdminController;
use App\Http\Controllers\Admin\IngredientAdminController;
use App\Http\Controllers\Admin\IngredientCategoryAdminController;
use App\Http\Controllers\Admin\ProfileAdminController;
use App\Http\Controllers\Admin\SupplierAdminController;
use App\Http\Controllers\Admin\StaffAdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware("guest")->group(function () {
    Route::name("login.")->group(function () {
        Route::get('/', [AuthController::class, 'loginPage'])->name('index');
        Route::post('/', [AuthController::class, 'login'])->name('submit')->middleware('throttle:login');
    });
});

Route::middleware("auth")->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::name("admin.")->prefix("admin")->middleware('auth')->group(function () {
    Route::get('/', [DashboardAdminController::class, 'index'])->name('dashboard');

    Route::name("account.")->prefix("account")->group(function () {
        Route::get('/', [ProfileAdminController::class, 'index'])->name('index');
        Route::post('update', [ProfileAdminController::class, 'update'])->name('update');
    });

    Route::name("supplier.")->prefix("supplier")->group(function () {
        Route::get('/', [SupplierAdminController::class, 'index'])->name('index');
        Route::post('/', [SupplierAdminController::class, 'store'])->name('store');
        Route::get('{id}', [SupplierAdminController::class, 'show'])->name('show');
        Route::patch('{id}', [SupplierAdminController::class, 'update'])->name('update');
        Route::delete('{id}', [SupplierAdminController::class, 'destroy'])->name('destroy');
    });

    Route::name("product.")->prefix("product")->group(function () {
        Route::get('/', [ProductAdminController::class, 'index'])->name('index');
        Route::post('/', [ProductAdminController::class, 'store'])->name('store');
        Route::get('{id}', [ProductAdminController::class, 'show'])->name('show');
        Route::patch('{id}', [ProductAdminController::class, 'update'])->name('update');
        Route::delete('{id}', [ProductAdminController::class, 'destroy'])->name('destroy');
    });

    Route::name("product_category.")->prefix("product-category")->group(function () {
        Route::get('/', [ProductCategoryAdminController::class, 'index'])->name('index');
        Route::post('store', [ProductCategoryAdminController::class, 'store'])->name('store');
        Route::get('select-search', [ProductCategoryAdminController::class, 'selectOption'])->name('select_search');
        Route::get('{id}', [ProductCategoryAdminController::class, 'show'])->name('show');
        Route::patch('{id}/update', [ProductCategoryAdminController::class, 'update'])->name('update');
        Route::delete('{id}', [ProductCategoryAdminController::class, 'destroy'])->name('destroy');
    });

        Route::name("ingredient.")->prefix("ingredient")->group(function () {
            Route::get('/', [IngredientAdminController::class, 'index'])->name('index');
            Route::post('store', [IngredientAdminController::class, 'store'])->name('store');
            Route::get('{id}', [IngredientAdminController::class, 'show'])->name('show');
            Route::post('{id}/update', [IngredientAdminController::class, 'update'])->name('update');
            Route::delete('{id}', [IngredientAdminController::class, 'destroy'])->name('destroy');
        });

        Route::name("ingredient_category.")->prefix("ingredient-category")->group(function () {
            Route::get('/', [IngredientCategoryAdminController::class, 'index'])->name('index');
            Route::post('store', [IngredientCategoryAdminController::class, 'store'])->name('store');
            Route::get('select-search', [IngredientCategoryAdminController::class, 'selectOption'])->name('select_search');
            Route::get('{id}', [IngredientCategoryAdminController::class, 'show'])->name('show');
            Route::post('{id}/update', [IngredientCategoryAdminController::class, 'update'])->name('update');
            Route::delete('{id}', [IngredientCategoryAdminController::class, 'destroy'])->name('destroy');
        });

    Route::name("staff.")->prefix("staff")->group(function () {
        Route::get("/", [StaffAdminController::class, "index"])->name("index");
        // Route::post("/")->name("store");
        Route::get("{id}", [StaffAdminController::class, "show"])->name("show");
        // Route::patch("/")->name("update");
        // Route::delete("/")->name("delete");
    });
});
