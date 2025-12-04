<?php

use App\Livewire\Modules\Quotes\Create;
use App\Livewire\Modules\Quotes\Index;
use App\Livewire\Modules\Quotes\Update;
use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,cotizaciones")
    ->prefix("quotes")
    ->name("quotes.")
    ->group(function () {
        Route::get("/", Index::class)->name("index");
        Route::get("/create", Create::class)->name("create");
        Route::get("/{quote}/edit", Update::class)->name("edit");
    });
