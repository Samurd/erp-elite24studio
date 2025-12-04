<?php

use App\Livewire\Modules\Donations\Campaigns\Index as CampaignsIndex;
use App\Livewire\Modules\Donations\Campaigns\Create as CampaignsCreate;
use App\Livewire\Modules\Donations\Campaigns\Update as CampaignsUpdate;

use App\Livewire\Modules\Donations\Donations\Index as DonationsIndex;
use App\Livewire\Modules\Donations\Donations\Create as DonationsCreate;
use App\Livewire\Modules\Donations\Donations\Update as DonationsUpdate;

use App\Livewire\Modules\Donations\Volunteers\Index as VolunteersIndex;
use App\Livewire\Modules\Donations\Volunteers\Create as VolunteersCreate;
use App\Livewire\Modules\Donations\Volunteers\Update as VolunteersUpdate;

use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,donaciones")
    ->prefix("donations")
    ->name("donations.")
    ->group(function () {

        Route::prefix('campaigns')->name('campaigns.')->group(function () {
            Route::get("/", CampaignsIndex::class)->name("index");
            Route::get("/create", CampaignsCreate::class)->name("create");
            Route::get("/{campaign}/edit", CampaignsUpdate::class)->name("edit");
        });

        Route::prefix('donations')->name('donations.')->group(function () {
            Route::get("/", DonationsIndex::class)->name("index");
            Route::get("/create", DonationsCreate::class)->name("create");
            Route::get("/{donation}/edit", DonationsUpdate::class)->name("edit");
        });

        Route::prefix('volunteers')->name('volunteers.')->group(function () {
            Route::get("/", VolunteersIndex::class)->name("index");
            Route::get("/create", VolunteersCreate::class)->name("create");
            Route::get("/{volunteer}/edit", VolunteersUpdate::class)->name("edit");
        });

        Route::prefix('alliances')->name('alliances.')->group(function () {
            Route::get("/", \App\Livewire\Modules\Donations\Alliances\Index::class)->name("index");
            Route::get("/create", \App\Livewire\Modules\Donations\Alliances\Create::class)->name("create");
            Route::get("/{alliance}/edit", \App\Livewire\Modules\Donations\Alliances\Update::class)->name("edit");
        });

        Route::prefix('apu-campaigns')->name('apu-campaigns.')->group(function () {
            Route::get("/", \App\Livewire\Modules\Donations\ApuCampaigns\Index::class)->name("index");
            Route::get("/create", \App\Livewire\Modules\Donations\ApuCampaigns\Create::class)->name("create");
            Route::get("/{apuCampaign}/edit", \App\Livewire\Modules\Donations\ApuCampaigns\Update::class)->name("edit");
        });
    });