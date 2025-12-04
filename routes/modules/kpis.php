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
        Route::get("/", Index::class)->name("index");
        Route::get("/create", Create::class)->name("create");
        Route::get("/{kpi}", Show::class)->name("show");
        Route::get("/{kpi}/edit", Update::class)->name("edit");
        
        // KPI Records routes
        Route::get("/{kpi}/records/create", RecordForm::class)->name("records.create");
        Route::get("/records/{record}/edit", RecordForm::class)->name("records.edit");
    });
