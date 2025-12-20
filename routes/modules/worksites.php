<?php

use App\Livewire\Modules\Worksites\Index;
use App\Livewire\Modules\Worksites\Create;
use App\Livewire\Modules\Worksites\Update;
use App\Livewire\Modules\Worksites\Show;
// use App\Livewire\Modules\Worksites\PunchItems\Create as PunchItemCreate;
// use App\Livewire\Modules\Worksites\PunchItems\Update as PunchItemUpdate;
// use App\Livewire\Modules\Worksites\PunchItems\Show as PunchItemShow;
use App\Http\Controllers\Modules\WorksitesPunchItemsController;
use App\Livewire\Modules\Worksites\Changes\Create as ChangeCreate;
use App\Livewire\Modules\Worksites\Update as ChangeUpdate;
use App\Livewire\Modules\Worksites\Changes\Show as ChangeShow;
// use App\Livewire\Modules\Worksites\Visits\Create as VisitCreate;
// use App\Livewire\Modules\Worksites\Visits\Show as VisitShow;
// use App\Livewire\Modules\Worksites\Visits\Update as VisitUpdate;
use App\Http\Controllers\Modules\WorksitesVisitsController;

use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,obras")
    ->prefix("worksites")
    ->name("worksites.")
    ->group(function () {
        Route::get("/", [\App\Http\Controllers\Modules\WorksitesController::class, 'index'])->name("index");
        Route::get("/create", [\App\Http\Controllers\Modules\WorksitesController::class, 'create'])->name("create");
        Route::get("/{worksite}/edit", [\App\Http\Controllers\Modules\WorksitesController::class, 'edit'])->name("edit");
        Route::get("/{worksite}", [\App\Http\Controllers\Modules\WorksitesController::class, 'show'])->name("show");
        Route::post("/", [\App\Http\Controllers\Modules\WorksitesController::class, 'store'])->name("store");
        Route::put("/{worksite}", [\App\Http\Controllers\Modules\WorksitesController::class, 'update'])->name("update");
        Route::delete("/{worksite}", [\App\Http\Controllers\Modules\WorksitesController::class, 'destroy'])->name("destroy");

        // Sub-module delete routes
        // Route::delete("/punch-items/{punchItem}", [\App\Http\Controllers\Modules\WorksitesController::class, 'destroyPunchItem'])->name("punch-items.destroy");
        // Route::delete("/changes/{change}", [\App\Http\Controllers\Modules\WorksitesController::class, 'destroyChange'])->name("changes.destroy"); // Migrated to WorksitesChangesController
// Route::delete("/visits/{visit}", [\App\Http\Controllers\Modules\WorksitesController::class, 'destroyVisit'])->name("visits.destroy");
    
        // Rutas para Punch Items
        Route::prefix("/{worksite}/punch-items")
            ->name("punch-items.")
            ->group(function () {
            Route::get("/create", [WorksitesPunchItemsController::class, 'create'])->name("create");
            Route::post("/", [WorksitesPunchItemsController::class, 'store'])->name("store");
            Route::get("/{punchItem}", [WorksitesPunchItemsController::class, 'show'])->name("show");
            Route::get("/{punchItem}/edit", [WorksitesPunchItemsController::class, 'edit'])->name("edit");
            Route::put("/{punchItem}", [WorksitesPunchItemsController::class, 'update'])->name("update");
            Route::delete("/{punchItem}", [WorksitesPunchItemsController::class, 'destroy'])->name("destroy");
        });

        // Rutas para Changes
        Route::prefix("/{worksite}/changes")
            ->name("changes.")
            ->group(function () {
            Route::get("/create", [\App\Http\Controllers\Modules\WorksitesChangesController::class, 'create'])->name("create");
            Route::post("/", [\App\Http\Controllers\Modules\WorksitesChangesController::class, 'store'])->name("store");
            Route::get("/{change}", [\App\Http\Controllers\Modules\WorksitesChangesController::class, 'show'])->name("show");
            Route::get("/{change}/edit", [\App\Http\Controllers\Modules\WorksitesChangesController::class, 'edit'])->name("edit");
            Route::put("/{change}", [\App\Http\Controllers\Modules\WorksitesChangesController::class, 'update'])->name("update");
            Route::delete("/{change}", [\App\Http\Controllers\Modules\WorksitesChangesController::class, 'destroy'])->name("destroy");
        });

        // Rutas para Visits
        Route::prefix("/{worksite}/visits")
            ->name("visits.")
            ->group(function () {
            Route::get("/create", [WorksitesVisitsController::class, 'create'])->name("create");
            Route::post("/", [WorksitesVisitsController::class, 'store'])->name("store");
            Route::get("/{visit}", [WorksitesVisitsController::class, 'show'])->name("show");
            Route::get("/{visit}/edit", [WorksitesVisitsController::class, 'edit'])->name("edit");
            Route::put("/{visit}", [WorksitesVisitsController::class, 'update'])->name("update");
            Route::delete("/{visit}", [WorksitesVisitsController::class, 'destroy'])->name("destroy");
        });

    });