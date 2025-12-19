<?php


use App\Livewire\Modules\Calendar\Index;

use Illuminate\Support\Facades\Route;


Route::prefix('calendar')->name('calendar.')->group(function () {
    Route::get('/', [\App\Http\Controllers\CalendarController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\CalendarController::class, 'store'])->name('store');
    Route::put('/{calendarEvent}', [\App\Http\Controllers\CalendarController::class, 'update'])->name('update');
    Route::delete('/{calendarEvent}', [\App\Http\Controllers\CalendarController::class, 'destroy'])->name('destroy');
});