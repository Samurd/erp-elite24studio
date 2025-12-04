<?php


use App\Livewire\Modules\Meetings\Show;
use App\Livewire\Modules\Meetings\Create;
use App\Livewire\Modules\Meetings\Index;
use App\Livewire\Modules\Meetings\Update;
use Illuminate\Support\Facades\Route;


Route::middleware('can-area:view,reuniones')->prefix('meetings')->name('meetings.')->group(function () {
    Route::get('/create', Create::class)->name('create');
    Route::get('/', Index::class)->name('index');
    Route::get('/{meeting}/edit', Update::class)->name('edit')->middleware('can-area:update,reuniones');
    Route::get('/{meeting}', Show::class)->name('show');
});