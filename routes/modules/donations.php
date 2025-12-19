<?php

use App\Http\Controllers\Modules\Donations\CampaignController;
use App\Http\Controllers\Modules\Donations\AllianceController;
use App\Http\Controllers\Modules\Donations\ApuCampaignController;
use App\Http\Controllers\Modules\Donations\DonationController;
use App\Http\Controllers\Modules\Donations\VolunteerController;

// use App\Livewire\Modules\Donations\Campaigns\Index as CampaignsIndex;
// use App\Livewire\Modules\Donations\Campaigns\Create as CampaignsCreate;
// use App\Livewire\Modules\Donations\Campaigns\Update as CampaignsUpdate;

// use App\Livewire\Modules\Donations\Donations\Index as DonationsIndex;
// use App\Livewire\Modules\Donations\Donations\Create as DonationsCreate;
// use App\Livewire\Modules\Donations\Donations\Update as DonationsUpdate;

// use App\Livewire\Modules\Donations\Volunteers\Index as VolunteersIndex;
// use App\Livewire\Modules\Donations\Volunteers\Create as VolunteersCreate;
// use App\Livewire\Modules\Donations\Volunteers\Update as VolunteersUpdate;

use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,donaciones")
    ->prefix("donations")
    ->name("donations.")
    ->group(function () {

        Route::prefix('campaigns')->name('campaigns.')->controller(CampaignController::class)->group(function () {
            Route::get("/", "index")->name("index");
            Route::get("/create", "create")->name("create");
            Route::post("/", "store")->name("store");
            Route::get("/{campaign}/edit", "edit")->name("edit");
            Route::put("/{campaign}", "update")->name("update");
            Route::delete("/{campaign}", "destroy")->name("destroy");
        });

        Route::prefix('donations')->name('donations.')->controller(DonationController::class)->group(function () {
            Route::get("/", "index")->name("index");
            Route::get("/create", "create")->name("create");
            Route::post("/", "store")->name("store");
            Route::get("/{donation}/edit", "edit")->name("edit");
            Route::put("/{donation}", "update")->name("update");
            Route::delete("/{donation}", "destroy")->name("destroy");
        });

        Route::prefix('volunteers')->name('volunteers.')->controller(VolunteerController::class)->group(function () {
            Route::get("/", "index")->name("index");
            Route::get("/create", "create")->name("create");
            Route::post("/", "store")->name("store");
            Route::get("/{volunteer}/edit", "edit")->name("edit");
            Route::put("/{volunteer}", "update")->name("update");
            Route::delete("/{volunteer}", "destroy")->name("destroy");
        });

        Route::prefix('alliances')->name('alliances.')->controller(AllianceController::class)->group(function () {
            Route::get("/", "index")->name("index");
            Route::get("/create", "create")->name("create");
            Route::post("/", "store")->name("store");
            Route::get("/{alliance}/edit", "edit")->name("edit");
            Route::put("/{alliance}", "update")->name("update");
            Route::delete("/{alliance}", "destroy")->name("destroy");
        });

        Route::prefix('apu-campaigns')->name('apu-campaigns.')->controller(ApuCampaignController::class)->group(function () {
            Route::get("/", "index")->name("index");
            Route::get("/create", "create")->name("create");
            Route::post("/", "store")->name("store");
            Route::get("/{apuCampaign}/edit", "edit")->name("edit");
            Route::put("/{apuCampaign}", "update")->name("update");
            Route::delete("/{apuCampaign}", "destroy")->name("destroy");
        });
    });