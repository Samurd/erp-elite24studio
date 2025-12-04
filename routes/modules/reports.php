<?php

use App\Livewire\Modules\Reports\Create;
use App\Livewire\Modules\Reports\Index;
use App\Livewire\Modules\Reports\Update;
use Illuminate\Support\Facades\Route;


Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', Index::class)->name('index')->middleware('can-area:view,reportes');;
    Route::get('/create', Create::class)->name('create');
    Route::get('/{report}/edit', Update::class)->name('edit');
});