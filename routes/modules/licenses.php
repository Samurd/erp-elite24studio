<?php

use App\Livewire\Modules\Licenses\Index;
use App\Livewire\Modules\Licenses\Create;
use App\Livewire\Modules\Licenses\Update;

use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,licencias")
    ->prefix("licenses")
    ->name("licenses.")
    ->group(function () {
        Route::get("/", Index::class)->name("index");
        Route::get("/create", Create::class)->name("create");
        Route::get("/{license}/edit", Update::class)->name("edit");
    });
