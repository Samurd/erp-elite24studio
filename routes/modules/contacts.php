<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Modules\Contacts\Index;
use App\Livewire\Modules\Contacts\CreateOrUpdate;

Route::prefix('contacts')->name('contacts.')->middleware('web')->group(function () {
    Route::get('/', [\App\Http\Controllers\ContactsController::class, 'index'])->name('index')->middleware('can-area:view,contactos');
    Route::get('/create', [\App\Http\Controllers\ContactsController::class, 'create'])->name('create')->middleware('can-area:create,contactos');
    Route::post('/', [\App\Http\Controllers\ContactsController::class, 'store'])->name('store');
    Route::get('/{contact}/edit', [\App\Http\Controllers\ContactsController::class, 'edit'])->name('edit')->middleware('can-area:update,contactos');
    Route::put('/{contact}', [\App\Http\Controllers\ContactsController::class, 'update'])->name('update');
    Route::delete('/{contact}', [\App\Http\Controllers\ContactsController::class, 'destroy'])->name('destroy')->middleware('can-area:delete,contactos');
});
