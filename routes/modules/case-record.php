<?php

use App\Livewire\Modules\CaseRecord\Create;
use App\Livewire\Modules\CaseRecord\Index;
use App\Livewire\Modules\CaseRecord\Update;
use Illuminate\Support\Facades\Route;


Route::prefix('case-record')->name('case-record.')->group(function () {
    Route::get('/', Index::class)->name('index');
    Route::get('/create', Create::class)->name('create');
    Route::get('/{caseRecord}/edit', Update::class)->name('edit');
});