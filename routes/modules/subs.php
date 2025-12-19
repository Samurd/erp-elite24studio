<?php

use App\Livewire\Modules\Subs\Create;
use App\Livewire\Modules\Subs\Index;
use App\Livewire\Modules\Subs\Update;
use Illuminate\Support\Facades\Route;


Route::prefix('subs')->name('subs.')->middleware('web')->group(function () {
    Route::get('/', [\App\Http\Controllers\SubscriptionsController::class, 'index'])->name('index')->middleware('can-area:view,suscripciones');
    Route::get('/create', [\App\Http\Controllers\SubscriptionsController::class, 'create'])->name('create')->middleware('can-area:create,suscripciones');
    Route::post('/', [\App\Http\Controllers\SubscriptionsController::class, 'store'])->name('store');
    Route::get('/{sub}/edit', [\App\Http\Controllers\SubscriptionsController::class, 'edit'])->name('edit')->middleware('can-area:update,suscripciones');
    Route::post('/{sub}', [\App\Http\Controllers\SubscriptionsController::class, 'update'])->name('update'); // Using POST for file upload spoofing
    Route::post('/{sub}/notification', [\App\Http\Controllers\SubscriptionsController::class, 'createNotification'])->name('notification');
    Route::delete('/{sub}', [\App\Http\Controllers\SubscriptionsController::class, 'destroy'])->name('destroy')->middleware('can-area:delete,suscripciones');
});