<?php

use App\Livewire\Modules\Worksites\Index;
use App\Livewire\Modules\Worksites\Create;
use App\Livewire\Modules\Worksites\Update;
use App\Livewire\Modules\Worksites\Show;
use App\Livewire\Modules\Worksites\PunchItems\Create as PunchItemCreate;
use App\Livewire\Modules\Worksites\PunchItems\Update as PunchItemUpdate;
use App\Livewire\Modules\Worksites\PunchItems\Show as PunchItemShow;
use App\Livewire\Modules\Worksites\Changes\Create as ChangeCreate;
use App\Livewire\Modules\Worksites\Update as ChangeUpdate;
use App\Livewire\Modules\Worksites\Changes\Show as ChangeShow;
use App\Livewire\Modules\Worksites\Visits\Create as VisitCreate;
use App\Livewire\Modules\Worksites\Visits\Show as VisitShow;
use App\Livewire\Modules\Worksites\Visits\Update as VisitUpdate;

use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,obras")
    ->prefix("worksites")
    ->name("worksites.")
    ->group(function () {
        Route::get("/", Index::class)->name("index");
        Route::get("/create", Create::class)->name("create");
        Route::get("/{worksite}/edit", Update::class)->name("edit");
        Route::get("/{worksite}", Show::class)->name("show");

        // Rutas para Punch Items
        Route::prefix("/{worksite}/punch-items")
            ->name("punch-items.")
            ->group(function () {
                Route::get("/create", PunchItemCreate::class)->name("create");
                Route::get("/{punchItem}", PunchItemShow::class)->name("show");
                Route::get("/{punchItem}/edit", PunchItemUpdate::class)->name("edit");
            });

        // Rutas para Changes
        Route::prefix("/{worksite}/changes")
            ->name("changes.")
            ->group(function () {
                Route::get("/create", ChangeCreate::class)->name("create");
                Route::get("/{change}", ChangeShow::class)->name("show");
                Route::get("/{change}/edit", ChangeUpdate::class)->name("edit");
            });

        // Rutas para Visits
        Route::prefix("/{worksite}/visits")
            ->name("visits.")
            ->group(function () {
                Route::get("/create", VisitCreate::class)->name("create");
                Route::get("/{visit}", VisitShow::class)->name("show");
                Route::get("/{visit}/edit", VisitUpdate::class)->name("edit");
            });

    });