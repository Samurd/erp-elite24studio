<?php

use App\Livewire\Modules\CaseRecord\Create;
use App\Livewire\Modules\CaseRecord\Index;
use App\Livewire\Modules\CaseRecord\Update;
use Illuminate\Support\Facades\Route;


Route::prefix('case-record')->name('case-record.')->middleware('web')->group(function () {
    Route::get('/', [\App\Http\Controllers\CaseRecordController::class, 'index'])->name('index')->middleware('can-area:view,registro-casos');
    Route::get('/create', [\App\Http\Controllers\CaseRecordController::class, 'create'])->name('create')->middleware('can-area:create,registro-casos');
    Route::post('/', [\App\Http\Controllers\CaseRecordController::class, 'store'])->name('store');
    Route::get('/{caseRecord}/edit', [\App\Http\Controllers\CaseRecordController::class, 'edit'])->name('edit')->middleware('can-area:update,registro-casos');
    Route::post('/{caseRecord}', [\App\Http\Controllers\CaseRecordController::class, 'update'])->name('update'); // Using POST for file upload spoofing
    Route::delete('/{caseRecord}', [\App\Http\Controllers\CaseRecordController::class, 'destroy'])->name('destroy')->middleware('can-area:delete,registro-casos');
});