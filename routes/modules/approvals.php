<?php


use App\Livewire\Modules\Approvals\Index;
use App\Livewire\Modules\Approvals\Create;
use App\Livewire\Modules\Approvals\Update;

use Illuminate\Support\Facades\Route;


Route::prefix('approvals')->name('approvals.')->middleware('web')->group(function () {
    Route::get('/', [\App\Http\Controllers\ApprovalsController::class, 'index'])->name('index')->middleware('can-area:view,aprobaciones');
    Route::get('/create', [\App\Http\Controllers\ApprovalsController::class, 'create'])->name('create')->middleware('can-area:create,aprobaciones');
    Route::post('/', [\App\Http\Controllers\ApprovalsController::class, 'store'])->name('store');

    Route::get('/{approval}/edit', [\App\Http\Controllers\ApprovalsController::class, 'edit'])->name('edit');
    Route::post('/{approval}/update', [\App\Http\Controllers\ApprovalsController::class, 'update'])->name('update');

    Route::get('/{approval}', [\App\Http\Controllers\ApprovalsController::class, 'show'])->name('show');
    Route::post('/{approval}/approve', [\App\Http\Controllers\ApprovalsController::class, 'approve'])->name('approve');
    Route::post('/{approval}/reject', [\App\Http\Controllers\ApprovalsController::class, 'reject'])->name('reject');
    Route::delete('/{approval}', [\App\Http\Controllers\ApprovalsController::class, 'destroy'])->name('destroy')->middleware('can-area:delete,aprobaciones');
});