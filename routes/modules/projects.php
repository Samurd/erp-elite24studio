<?php

use App\Livewire\Modules\Projects\Index;
use App\Livewire\Modules\Projects\Create;
use App\Livewire\Modules\Projects\Update;
use App\Livewire\Modules\Projects\Show;

use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,proyectos")
    ->prefix("projects")
    ->name("projects.")
    ->group(function () {
        Route::get("/", [\App\Http\Controllers\Modules\ProjectsController::class, 'index'])->name("index");
        Route::get("/create", [\App\Http\Controllers\Modules\ProjectsController::class, 'create'])->name("create");
        Route::post("/", [\App\Http\Controllers\Modules\ProjectsController::class, 'store'])->name("store");
        Route::get("/{project}/edit", [\App\Http\Controllers\Modules\ProjectsController::class, 'edit'])->name("edit");
        Route::put("/{project}", [\App\Http\Controllers\Modules\ProjectsController::class, 'update'])->name("update"); // Changed to PUT/POST as needed
        Route::post("/{project}/update-post", [\App\Http\Controllers\Modules\ProjectsController::class, 'update'])->name("update-post"); // For file uploads if needed
        Route::get("/{project}", [\App\Http\Controllers\Modules\ProjectsController::class, 'show'])->name("show");
        Route::delete("/{project}", [\App\Http\Controllers\Modules\ProjectsController::class, 'destroy'])->name("destroy");

        // Stage deletion route
        Route::delete("/stages/{stage}", [\App\Http\Controllers\Modules\ProjectsController::class, 'destroyStage'])->name("stages.destroy");

        Route::get("/{project}/plans/{plan}/export-gantt", [App\Http\Controllers\ProjectPdfController::class, "exportGantt"])->name("plans.export-gantt");
    });