<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Modules\Contacts\Index;
use App\Livewire\Modules\Contacts\CreateOrUpdate;

Route::prefix('contacts')->name('contacts.')->group(function () {
    Route::get('/', Index::class)->name('index')->middleware('can-area:view,contactos');;
    Route::get('/create', CreateOrUpdate::class)->name('create')->middleware('can-area:create,contactos');;
    Route::get('/{contact}/edit', CreateOrUpdate::class)->name('edit')->middleware('can-area:update,contactos');;
});
