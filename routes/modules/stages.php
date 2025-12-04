<?php

use App\Livewire\Modules\Stages\Index;
use App\Livewire\Modules\Stages\Create;
use App\Livewire\Modules\Stages\Update;

use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,proyectos")
    ->prefix("stages")
    ->name("stages.")
    ->group(function () {
        Route::get("/", Index::class)->name("index");
        Route::get("/create", Create::class)->name("create");
        Route::get("/{stage}/edit", Update::class)->name("edit");
    });