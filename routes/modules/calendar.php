<?php


use App\Livewire\Modules\Calendar\Index;

use Illuminate\Support\Facades\Route;


Route::prefix('calendar')->name('calendar.')->group(function () {
    Route::get('/', Index::class)->name('index');
    // Route::get('/create', Create::class)->name('create');
    // Route::get('/{caseRecord}/edit', Update::class)->name('edit');
});