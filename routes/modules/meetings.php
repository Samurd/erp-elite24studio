<?php


use App\Livewire\Modules\Meetings\Show;
use App\Livewire\Modules\Meetings\Create;
use App\Livewire\Modules\Meetings\Index;
use App\Livewire\Modules\Meetings\Update;
use Illuminate\Support\Facades\Route;


Route::middleware('can-area:view,reuniones')->prefix('meetings')->name('meetings.')->group(function () {
    Route::get('/', [\App\Http\Controllers\MeetingsController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\MeetingsController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\MeetingsController::class, 'store'])->name('store');
    Route::get('/{meeting}', [\App\Http\Controllers\MeetingsController::class, 'show'])->name('show');
    Route::get('/{meeting}/edit', [\App\Http\Controllers\MeetingsController::class, 'edit'])->name('edit')->middleware('can-area:update,reuniones');
    Route::put('/{meeting}', [\App\Http\Controllers\MeetingsController::class, 'update'])->name('update');
    Route::delete('/{meeting}', [\App\Http\Controllers\MeetingsController::class, 'destroy'])->name('destroy');
});