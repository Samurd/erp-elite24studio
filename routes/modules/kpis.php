<?php

use App\Livewire\Modules\Kpis\Index;
use App\Livewire\Modules\Kpis\Show;
use App\Livewire\Modules\Kpis\Create;
use App\Livewire\Modules\Kpis\Update;
use App\Livewire\Modules\Kpis\RecordForm;

use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,kpis")
    ->prefix("kpis")
    ->name("kpis.")
    ->group(function () {
        // Module KPIs
        Route::controller(App\Http\Controllers\Modules\Kpis\KpiController::class)->group(function () {
            Route::get("/", "index")->name("index");
            Route::get("/create", "create")->name("create");
            Route::post("/", "store")->name("store");
            Route::get("/{kpi}", "show")->name("show");
            Route::get("/{kpi}/edit", "edit")->name("edit");
            Route::put("/{kpi}", "update")->name("update");
            Route::delete("/{kpi}", "destroy")->name("destroy");
        });

        // KPI Records routes
        Route::controller(App\Http\Controllers\Modules\Kpis\KpiRecordController::class)->group(function () {
            Route::get("/{kpi}/records/create", "create")->name("records.create");
            Route::post("/{kpi}/records", "store")->name("records.store");
            Route::get("/records/{kpiRecord}/edit", "edit")->name("records.edit");
            Route::put("/records/{kpiRecord}", "update")->name("records.update");
            Route::delete("/records/{kpiRecord}", "destroy")->name("records.destroy");
            Route::delete("/records/files/{file}", "deleteFile")->name("records.files.destroy");
        });
    });
