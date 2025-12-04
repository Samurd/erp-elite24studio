<?php

use App\Livewire\Modules\Notifications\Index;
use App\Livewire\Modules\Notifications\Create;
use App\Livewire\Modules\Notifications\Update;

use Illuminate\Support\Facades\Route;

Route::prefix("notifications")
    ->name("notifications.")
    ->group(function () {
        Route::get("/", Index::class)->name("index");
        Route::get("/create", Create::class)->name("create");
        Route::get("/{notification}/edit", Update::class)->name("edit");
    });
