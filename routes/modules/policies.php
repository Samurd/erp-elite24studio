<?php

use App\Livewire\Modules\Policies\Create;
use App\Livewire\Modules\Policies\Show;
use App\Livewire\Modules\Policies\Index;
use App\Livewire\Modules\Policies\Update;
use Illuminate\Support\Facades\Route;

Route::prefix('policies')->name('policies.')->group(function () {
    Route::get('/create', Create::class)->name('create')->middleware('can-area:create,politicas');
    
    Route::get('/', Index::class)->name('index')->middleware('can-area:view,politicas');
    Route::get('/{policy}', Show::class)->name('show');
    Route::get('/{policy}/edit', Update::class)->name('edit')->middleware('can-area:update,politicas');
});