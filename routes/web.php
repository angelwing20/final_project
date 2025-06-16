<?php

use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\StaffAdminController;
use Illuminate\Support\Facades\Route;

Route::name("admin.")->prefix("admin")->group(function () {
    Route::get("/", [DashboardAdminController::class, "index"])->name("dashboard");

    Route::name("staff.")->prefix("staff")->group(function () {
        Route::get("/", [StaffAdminController::class, "index"])->name("index");
        // Route::post("/")->name("store");
        Route::get("{id}", [StaffAdminController::class, "show"])->name("show");
        // Route::patch("/")->name("update");
        // Route::delete("/")->name("delete");
    });
});
