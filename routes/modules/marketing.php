<?php

// use App\Livewire\Modules\Finances\Expense\Index as ExpenseIndex;
// use App\Livewire\Modules\Finances\Gross\Index as GrossIndex;
// use App\Livewire\Modules\Finances\Index;

use App\Livewire\Modules\Rrhh\Index;
use App\Livewire\Modules\Rrhh\Marketing\Index as MarketingIndex;

// use App\Livewire\Modules\Marketing\Strategies\Index as StrategiesIndex;
// use App\Livewire\Modules\Marketing\Strategies\Create as StrategiesCreate;
// use App\Livewire\Modules\Marketing\Strategies\Update as StrategiesUpdate;

use App\Http\Controllers\Modules\Marketing\StrategiesController;

// use App\Livewire\Modules\Marketing\SocialMediaPost\Index as SocialMediaPostIndex;
// use App\Livewire\Modules\Marketing\SocialMediaPost\Create as SocialMediaPostCreate;
// use App\Livewire\Modules\Marketing\SocialMediaPost\Update as SocialMediaPostUpdate;

use App\Http\Controllers\Modules\Marketing\SocialMediaPostController;

// use App\Livewire\Modules\Marketing\Cases\Index as CaseIndex;
// use App\Livewire\Modules\Marketing\Cases\Create as CaseCreate;
// use App\Livewire\Modules\Marketing\Cases\Update as CaseUpdate;

use App\Http\Controllers\Modules\Marketing\CasesController;

// use App\Livewire\Modules\Marketing\AdPieces\Index as AdPiecesIndex;
// use App\Livewire\Modules\Marketing\AdPieces\Create as AdPiecesCreate;
// use App\Livewire\Modules\Marketing\AdPieces\Update as AdPiecesUpdate;

use App\Http\Controllers\Modules\Marketing\AdPieceController;

// use App\Livewire\Modules\Marketing\Events\Index as EventsIndex;
// use App\Livewire\Modules\Marketing\Events\Create as EventsCreate;
// use App\Livewire\Modules\Marketing\Events\Update as EventsUpdate;
// use App\Livewire\Modules\Marketing\Events\Show as EventsShow;
// use App\Livewire\Modules\Marketing\Events\Items\Create as EventItemsCreate;
// use App\Livewire\Modules\Marketing\Events\Items\Update as EventItemsUpdate;

use App\Http\Controllers\Modules\Marketing\EventsController;



use Illuminate\Support\Facades\Route;

Route::middleware('can-area:view,marketing')
    ->prefix('marketing')
    ->name('marketing.')
    ->group(function () {
        Route::get('/', Index::class)->name('index');

        Route::prefix('strategies')->name('strategies.')->controller(StrategiesController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{strategy}/edit', 'edit')->name('edit');
            Route::put('/{strategy}', 'update')->name('update');
            Route::delete('/{strategy}', 'destroy')->name('destroy');
        });

        Route::prefix('socialmedia')->name('socialmedia.')->controller(SocialMediaPostController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{post}/edit', 'edit')->name('edit');
            Route::put('/{post}', 'update')->name('update');
            Route::delete('/{post}', 'destroy')->name('destroy');
        });

        Route::prefix('cases')->name('cases.')->controller(CasesController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{caseMarketing}/edit', 'edit')->name('edit');
            Route::put('/{caseMarketing}', 'update')->name('update');
            Route::delete('/{caseMarketing}', 'destroy')->name('destroy');
        });

        Route::prefix('ad-pieces')->name('ad-pieces.')->controller(AdPieceController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{adpiece}/edit', 'edit')->name('edit');
            Route::put('/{adpiece}', 'update')->name('update');
            Route::delete('/{adpiece}', 'destroy')->name('destroy');
        });


        Route::controller(EventsController::class)->prefix('events')->name('events.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{event}/edit', 'edit')->name('edit');
            Route::put('/{event}', 'update')->name('update');
            Route::delete('/{event}', 'destroy')->name('destroy');
            Route::get('/{event}', 'show')->name('show');

            // Event items routes
            Route::prefix('{event}/items')->name('items.')->group(function () {
                Route::get('/create', 'createItem')->name('create');
                Route::post('/', 'storeItem')->name('store');
                Route::get('/{eventItem}/edit', 'editItem')->name('edit');
                Route::put('/{eventItem}', 'updateItem')->name('update');
                Route::delete('/{eventItem}', 'destroyItem')->name('destroy');
            });
        });


    });
