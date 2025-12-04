<?php

use App\Livewire\Modules\Subs\Create;
use App\Livewire\Modules\Subs\Index;
use App\Livewire\Modules\Subs\Update;
use Illuminate\Support\Facades\Route;


Route::prefix('subs')->name('subs.')->group(function () {
    Route::get('/', Index::class)->name('index')->middleware('can-area:view,suscripciones');;
    Route::get('/create', Create::class)->name('create');
    Route::get('/{sub}/edit', Update::class)->name('edit');
});