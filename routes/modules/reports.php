<?php

use App\Livewire\Modules\Reports\Create;
use App\Livewire\Modules\Reports\Index;
use App\Livewire\Modules\Reports\Update;
use Illuminate\Support\Facades\Route;


Route::prefix('reports')->name('reports.')->middleware('web')->group(function () {
    Route::get('/', [\App\Http\Controllers\ReportsController::class, 'index'])->name('index')->middleware('can-area:view,reportes');
    Route::get('/create', [\App\Http\Controllers\ReportsController::class, 'create'])->name('create')->middleware('can-area:create,reportes');
    Route::post('/', [\App\Http\Controllers\ReportsController::class, 'store'])->name('store');
    Route::get('/{report}/edit', [\App\Http\Controllers\ReportsController::class, 'edit'])->name('edit')->middleware('can-area:update,reportes');
    Route::post('/{report}', [\App\Http\Controllers\ReportsController::class, 'update'])->name('update'); // Using POST for file upload spoofing
    Route::delete('/{report}', [\App\Http\Controllers\ReportsController::class, 'destroy'])->name('destroy')->middleware('can-area:delete,reportes');
});