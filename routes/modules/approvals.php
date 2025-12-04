<?php


use App\Livewire\Modules\Approvals\Index;
use App\Livewire\Modules\Approvals\Create;
use App\Livewire\Modules\Approvals\Update;

use Illuminate\Support\Facades\Route;


Route::prefix('approvals')->name('approvals.')->group(function () {
    Route::get('/', Index::class)->name('index')->middleware('can-area:view,aprobaciones');
    Route::get('/create', Create::class)->name('create')->middleware('can-area:create,aprobaciones');
    Route::get('/{approval}/edit', Update::class)->name('edit')->middleware('can-area:update,aprobaciones');
});