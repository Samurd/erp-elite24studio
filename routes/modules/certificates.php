<?php

use App\Livewire\Modules\Certificates\Create;
use App\Livewire\Modules\Certificates\Show;
use App\Livewire\Modules\Certificates\Index;
use App\Livewire\Modules\Certificates\Update;
use Illuminate\Support\Facades\Route;

Route::prefix('certificates')->name('certificates.')->middleware('web')->group(function () {
    Route::get('/', [\App\Http\Controllers\CertificatesController::class, 'index'])->name('index')->middleware('can-area:view,certificados');
    Route::get('/create', [\App\Http\Controllers\CertificatesController::class, 'create'])->name('create')->middleware('can-area:create,certificados');
    Route::post('/', [\App\Http\Controllers\CertificatesController::class, 'store'])->name('store');
    Route::get('/{certificate}', [\App\Http\Controllers\CertificatesController::class, 'show'])->name('show');
    Route::get('/{certificate}/edit', [\App\Http\Controllers\CertificatesController::class, 'edit'])->name('edit')->middleware('can-area:update,certificados');
    Route::post('/{certificate}', [\App\Http\Controllers\CertificatesController::class, 'update'])->name('update');
    Route::delete('/{certificate}', [\App\Http\Controllers\CertificatesController::class, 'destroy'])->name('destroy')->middleware('can-area:delete,certificados');
});