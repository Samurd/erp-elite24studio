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
        Route::get("/", Index::class)->name("index");
        Route::get("/create", Create::class)->name("create");
        Route::get("/{project}/edit", Update::class)->name("edit");
        Route::get("/{project}", Show::class)->name("show");
    });