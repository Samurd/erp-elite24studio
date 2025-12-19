<?php

use App\Livewire\Modules\Notifications\Index;
use App\Livewire\Modules\Notifications\Create;
use App\Livewire\Modules\Notifications\Update;

use Illuminate\Support\Facades\Route;

Route::prefix("notifications")
    ->name("notifications.")
    ->group(function () {
        Route::get("/", [\App\Http\Controllers\Modules\NotificationController::class, 'index'])->name("index");
        Route::get("/create", [\App\Http\Controllers\Modules\NotificationController::class, 'create'])->name("create");
        Route::post("/", [\App\Http\Controllers\Modules\NotificationController::class, 'store'])->name("store");
        Route::get("/{notification}/edit", [\App\Http\Controllers\Modules\NotificationController::class, 'edit'])->name("edit");
        Route::put("/{notification}", [\App\Http\Controllers\Modules\NotificationController::class, 'update'])->name("update");
        Route::delete("/{notification}", [\App\Http\Controllers\Modules\NotificationController::class, 'destroy'])->name("destroy");
        Route::post("/{notification}/toggle-status", [\App\Http\Controllers\Modules\NotificationController::class, 'toggleStatus'])->name("toggle-status");

        // API Routes for Vue Component
        Route::get("/api/list", [\App\Http\Controllers\Api\NotificationController::class, 'index'])->name("api.index");
        Route::post("/api/{id}/read", [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead'])->name("api.read");
        Route::post("/api/read-all", [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead'])->name("api.read-all");
        Route::delete("/api/{id}", [\App\Http\Controllers\Api\NotificationController::class, 'destroy'])->name("api.destroy");
    });
