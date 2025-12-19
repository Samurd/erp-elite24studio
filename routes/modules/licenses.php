<?php

use App\Livewire\Modules\Licenses\Index;
use App\Livewire\Modules\Licenses\Create;
use App\Livewire\Modules\Licenses\Update;

use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,licencias")
    ->prefix("licenses")
    ->name("licenses.")
    ->group(function () {
        Route::get("/", [\App\Http\Controllers\LicensesController::class, 'index'])->name("index");
        Route::get("/create", [\App\Http\Controllers\LicensesController::class, 'create'])->name("create")->middleware('can-area:create,licencias');
        Route::post("/", [\App\Http\Controllers\LicensesController::class, 'store'])->name("store");
        Route::get("/{license}/edit", [\App\Http\Controllers\LicensesController::class, 'edit'])->name("edit")->middleware('can-area:update,licencias');
        Route::post("/{license}", [\App\Http\Controllers\LicensesController::class, 'update'])->name("update");
        Route::delete("/{license}", [\App\Http\Controllers\LicensesController::class, 'destroy'])->name("destroy")->middleware('can-area:delete,licencias');
    });
