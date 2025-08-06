<?php

use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\ImportDailySalesAdminController;
use App\Http\Controllers\Admin\IngredientAdminController;
use App\Http\Controllers\Admin\IngredientCategoryAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\ProductCategoryAdminController;
use App\Http\Controllers\Admin\ProductIngredientAdminController;
use App\Http\Controllers\Admin\AddOnAdminController;
use App\Http\Controllers\Admin\AddOnIngredientAdminController;
use App\Http\Controllers\Admin\ProfileAdminController;
use App\Http\Controllers\Admin\DailySalesAdminController;
use App\Http\Controllers\Admin\DailySalesItemAdminController;
use App\Http\Controllers\Admin\RefillStockHistoryAdminController;
use App\Http\Controllers\Admin\StaffAdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PasswordResetController;

Route::middleware("guest")->group(function () {
    Route::name("login.")->group(function () {
        Route::get('/', [AuthController::class, 'loginPage'])->name('index');
        Route::post('/', [AuthController::class, 'login'])->name('submit')->middleware('throttle:login');
    });
});

Route::middleware("auth")->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::name('forgot_password.')->prefix('forgot-password')->group(function () {
    Route::get('/', [ForgotPasswordController::class, 'index'])->name('index');
    Route::post('/', [ForgotPasswordController::class, 'forgotPassword'])->name('request');
});

Route::name('reset_password.')->prefix('reset-password')->group(function () {
    Route::get('{token}/{email}', [PasswordResetController::class, 'index'])->name('index');
    Route::post('/', [PasswordResetController::class, 'passwordReset'])->name('request');
});

Route::name("admin.")->prefix("admin")->middleware('auth')->group(function () {
    Route::get('/', [DashboardAdminController::class, 'index'])->name('dashboard');
    Route::get('ingredient-consumption', [DashboardAdminController::class, 'getIngredientConsumptionData'])->name('ingredient_consumption');
    Route::get('sales-trend', [DashboardAdminController::class, 'getSalesTrendData'])->name('sales_trend');
    Route::get('stats', [DashboardAdminController::class, 'getDashboardStats'])->name('stats');

    Route::post('/', [ImportDailySalesAdminController::class, 'importDailySales'])->name('import_daily_sales');

    Route::name("account.")->prefix("account")->group(function () {
        Route::get('/', [ProfileAdminController::class, 'index'])->name('profile');
        Route::patch('/', [ProfileAdminController::class, 'update'])->name('update');
        Route::patch('update-password', [ProfileAdminController::class, 'updatePassword'])->name('update_password');
    });

    Route::name("ingredient.")->prefix("ingredient")->group(function () {
        Route::get('/', [IngredientAdminController::class, 'index'])->name('index');
        Route::post('store', [IngredientAdminController::class, 'store'])->name('store');
        Route::get('select-search', [IngredientAdminController::class, 'selectOption'])->name('select_search');
        Route::get('{id}', [IngredientAdminController::class, 'show'])->name('show');
        Route::patch('refill-stock', [IngredientAdminController::class, 'refillStock'])->name('refill_stock');
        Route::patch('{id}', [IngredientAdminController::class, 'update'])->name('update');
        Route::delete('{id}', [IngredientAdminController::class, 'destroy'])->name('destroy');
    });

    Route::name("ingredient_category.")->prefix("ingredient-category")->group(function () {
        Route::get('/', [IngredientCategoryAdminController::class, 'index'])->name('index');
        Route::post('store', [IngredientCategoryAdminController::class, 'store'])->name('store');
        Route::get('select-search', [IngredientCategoryAdminController::class, 'selectOption'])->name('select_search');
        Route::get('{id}', [IngredientCategoryAdminController::class, 'show'])->name('show');
        Route::patch('{id}', [IngredientCategoryAdminController::class, 'update'])->name('update');
        Route::delete('{id}', [IngredientCategoryAdminController::class, 'destroy'])->name('destroy');
    });

    Route::name("product.")->prefix("product")->group(function () {
        Route::get('/', [ProductAdminController::class, 'index'])->name('index');
        Route::post('/', [ProductAdminController::class, 'store'])->name('store');
        Route::get('{id}', [ProductAdminController::class, 'show'])->name('show');
        Route::patch('{id}', [ProductAdminController::class, 'update'])->name('update');
        Route::delete('{id}', [ProductAdminController::class, 'destroy'])->name('destroy');

        Route::name("ingredient.")->prefix("ingredient")->group(function () {
            Route::post('/', [ProductIngredientAdminController::class, 'store'])->name('store');
            Route::delete('{id}', [ProductIngredientAdminController::class, 'destroy'])->name('destroy');
        });
    });

    Route::name("product_category.")->prefix("product-category")->group(function () {
        Route::get('/', [ProductCategoryAdminController::class, 'index'])->name('index');
        Route::post('store', [ProductCategoryAdminController::class, 'store'])->name('store');
        Route::get('select-search', [ProductCategoryAdminController::class, 'selectOption'])->name('select_search');
        Route::get('{id}', [ProductCategoryAdminController::class, 'show'])->name('show');
        Route::patch('{id}/update', [ProductCategoryAdminController::class, 'update'])->name('update');
        Route::delete('{id}', [ProductCategoryAdminController::class, 'destroy'])->name('destroy');
    });

    Route::name("add_on.")->prefix("add-on")->group(function () {
        Route::get('/', [AddOnAdminController::class, 'index'])->name('index');
        Route::post('store', [AddOnAdminController::class, 'store'])->name('store');
        Route::get('{id}', [AddOnAdminController::class, 'show'])->name('show');
        Route::patch('{id}/update', [AddOnAdminController::class, 'update'])->name('update');
        Route::delete('{id}', [AddOnAdminController::class, 'destroy'])->name('destroy');

        Route::name("ingredient.")->prefix("ingredient")->group(function () {
            Route::post('/', [AddOnIngredientAdminController::class, 'store'])->name('store');
            Route::delete('{id}', [AddOnIngredientAdminController::class, 'destroy'])->name('destroy');
        });
    });

    Route::name("daily_sales.")->prefix("daily-sales")->group(function () {
        Route::get('/', [DailySalesAdminController::class, "index"])->name("index");

        Route::get('create', [DailySalesItemAdminController::class, "create"])->name("create");
        Route::get('edit/{id}', [DailySalesItemAdminController::class, "edit"])->name("edit");

        Route::get('{id}', [DailySalesAdminController::class, "show"])->name("show");

        Route::post('/', [DailySalesItemAdminController::class, "store"])->name("store");
        Route::patch('{id}', [DailySalesItemAdminController::class, "update"])->name("update");
        Route::delete('{id}', [DailySalesItemAdminController::class, "destroy"])->name("destroy");
    });

    Route::name("refill_stock_history.")->prefix("refill-stock-history")->group(function () {
        Route::get('/', [RefillStockHistoryAdminController::class, 'index'])->name('index');
    });

    Route::name("staff.")->prefix("staff")->group(function () {
        Route::get('/', [StaffAdminController::class, "index"])->name("index");
        Route::post('/', [StaffAdminController::class, "store"])->name("store");
        Route::get('select-search', [StaffAdminController::class, 'selectOption'])->name('select_search');
        Route::get('{id}', [StaffAdminController::class, "show"])->name("show");
        Route::patch('update-password/{id}', [StaffAdminController::class, 'updatePassword'])->name('update_password');
        Route::patch('{id}', [StaffAdminController::class, "update"])->name("update");
        Route::delete('{id}', [StaffAdminController::class, "destroy"])->name("destroy");
    });
});
