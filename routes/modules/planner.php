<?php


use App\Livewire\Modules\Planner\Index;

use Illuminate\Support\Facades\Route;


Route::prefix('planner')->name('planner.')->group(function () {
    Route::get('/', Index::class)->name('index');
    // Route::get('/create', Create::class)->name('create');
    // Route::get('/{caseRecord}/edit', Update::class)->name('edit');
});