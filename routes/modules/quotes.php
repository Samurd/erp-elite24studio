<?php

use App\Http\Controllers\QuotesController;
use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,cotizaciones")
    ->prefix("quotes")
    ->name("quotes.")
    ->group(function () {
        Route::get("/", [QuotesController::class, 'index'])->name("index");
        Route::get("/create", [QuotesController::class, 'create'])->name("create");
        Route::post("/", [QuotesController::class, 'store'])->name("store");
        Route::get("/{quote}/edit", [QuotesController::class, 'edit'])->name("edit");
        Route::put("/{quote}", [QuotesController::class, 'update'])->name("update");
        Route::delete("/{quote}", [QuotesController::class, 'destroy'])->name("destroy");
    });