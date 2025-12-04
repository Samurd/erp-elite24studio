<?php

// use App\Livewire\Modules\Finances\Expense\Index as ExpenseIndex;
// use App\Livewire\Modules\Finances\Gross\Index as GrossIndex;
// use App\Livewire\Modules\Finances\Index;

use App\Livewire\Modules\Rrhh\Index;
use App\Livewire\Modules\Rrhh\Marketing\Index as MarketingIndex;

use App\Livewire\Modules\Marketing\Strategies\Index as StrategiesIndex;
use App\Livewire\Modules\Marketing\Strategies\Create as StrategiesCreate;
use App\Livewire\Modules\Marketing\Strategies\Update as StrategiesUpdate;

use App\Livewire\Modules\Marketing\SocialMediaPost\Index as SocialMediaPostIndex;
use App\Livewire\Modules\Marketing\SocialMediaPost\Create as SocialMediaPostCreate;
use App\Livewire\Modules\Marketing\SocialMediaPost\Update as SocialMediaPostUpdate;

use App\Livewire\Modules\Marketing\Cases\Index as CaseIndex;
use App\Livewire\Modules\Marketing\Cases\Create as CaseCreate;
use App\Livewire\Modules\Marketing\Cases\Update as CaseUpdate;

use App\Livewire\Modules\Marketing\AdPieces\Index as AdPiecesIndex;
use App\Livewire\Modules\Marketing\AdPieces\Create as AdPiecesCreate;
use App\Livewire\Modules\Marketing\AdPieces\Update as AdPiecesUpdate;

use App\Livewire\Modules\Marketing\Events\Index as EventsIndex;
use App\Livewire\Modules\Marketing\Events\Create as EventsCreate;
use App\Livewire\Modules\Marketing\Events\Update as EventsUpdate;
use App\Livewire\Modules\Marketing\Events\Show as EventsShow;
use App\Livewire\Modules\Marketing\Events\Items\Create as EventItemsCreate;
use App\Livewire\Modules\Marketing\Events\Items\Update as EventItemsUpdate;



use Illuminate\Support\Facades\Route;

Route::middleware('can-area:view,marketing')
    ->prefix('marketing')
    ->name('marketing.')
    ->group(function () {
        Route::get('/', Index::class)->name('index');

        Route::prefix('strategies')->name('strategies.')->group(function () {
            Route::get('/', StrategiesIndex::class)->name('index');
            Route::get('/create', StrategiesCreate::class)->name('create');
            Route::get('/{strategy}/edit', StrategiesUpdate::class)->name('edit');
        });

        Route::prefix('socialmedia')->name('socialmedia.')->group(function () {
            Route::get('/', SocialMediaPostIndex::class)->name('index');
            Route::get('/create', SocialMediaPostCreate::class)->name('create');
            Route::get('/{post}/edit', SocialMediaPostUpdate::class)->name('edit');
        });

        Route::prefix('cases')->name('cases.')->group(function () {
            Route::get('/', CaseIndex::class)->name('index');
            Route::get('/create', CaseCreate::class)->name('create');
            Route::get('/{caseMarketing}/edit', CaseUpdate::class)->name('edit');
        });

        Route::prefix('ad-pieces')->name('ad-pieces.')->group(function () {
            Route::get('/', AdPiecesIndex::class)->name('index');
            Route::get('/create', AdPiecesCreate::class)->name('create');
            Route::get('/{adpiece}/edit', AdPiecesUpdate::class)->name('edit');
        });
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', EventsIndex::class)->name('index');
            Route::get('/create', EventsCreate::class)->name('create');
            Route::get('/{event}/edit', EventsUpdate::class)->name('edit');
            Route::get('/{event}', EventsShow::class)->name('show');
            
            // Event items routes
            Route::prefix('{event}/items')->name('items.')->group(function () {
                Route::get('/create', EventItemsCreate::class)->name('create');
                Route::get('/{eventItem}/edit', EventItemsUpdate::class)->name('edit');
            });
        });


    });
