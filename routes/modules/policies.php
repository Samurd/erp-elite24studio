<?php

use App\Livewire\Modules\Policies\Create;
use App\Livewire\Modules\Policies\Show;
use App\Livewire\Modules\Policies\Index;
use App\Livewire\Modules\Policies\Update;
use Illuminate\Support\Facades\Route;

Route::prefix('policies')->name('policies.')->middleware('web')->group(function () {
    Route::get('/', [\App\Http\Controllers\PoliciesController::class, 'index'])->name('index')->middleware('can-area:view,politicas');
    Route::get('/create', [\App\Http\Controllers\PoliciesController::class, 'create'])->name('create')->middleware('can-area:create,politicas');
    Route::post('/', [\App\Http\Controllers\PoliciesController::class, 'store'])->name('store');
    Route::get('/{policy}', [\App\Http\Controllers\PoliciesController::class, 'show'])->name('show');
    Route::get('/{policy}/edit', [\App\Http\Controllers\PoliciesController::class, 'edit'])->name('edit')->middleware('can-area:update,politicas');
    Route::post('/{policy}', [\App\Http\Controllers\PoliciesController::class, 'update'])->name('update');
    Route::delete('/{policy}', [\App\Http\Controllers\PoliciesController::class, 'destroy'])->name('destroy')->middleware('can-area:delete,politicas');
});