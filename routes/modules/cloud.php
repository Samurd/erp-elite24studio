<?php

use App\Livewire\Modules\Cloud\Index;
use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,cloud")
    ->prefix("cloud")
    ->name("cloud.")
    ->group(function () {
        Route::get("/", Index::class)->name("index");
    });
