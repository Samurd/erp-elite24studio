<?php

use App\Livewire\Modules\Certificates\Create;
use App\Livewire\Modules\Certificates\Show;
use App\Livewire\Modules\Certificates\Index;
use App\Livewire\Modules\Certificates\Update;
use Illuminate\Support\Facades\Route;

Route::prefix('certificates')->name('certificates.')->group(function () {
    Route::get('/create', Create::class)->name('create')->middleware('can-area:create,certificados');
    
    Route::get('/', Index::class)->name('index')->middleware('can-area:view,certificados');
    Route::get('/{certificate}', Show::class)->name('show');
    Route::get('/{certificate}/edit', Update::class)->name('edit')->middleware('can-area:update,certificados');
});