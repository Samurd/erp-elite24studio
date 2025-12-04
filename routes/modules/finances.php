<?php

use App\Livewire\Modules\Finances\Expense\Index as ExpenseIndex;
use App\Livewire\Modules\Finances\Gross\Index as GrossIndex;
use App\Livewire\Modules\Finances\Net\Index as NetIndex;
use App\Livewire\Modules\Finances\Index;

use App\Livewire\Modules\Finances\Payrolls\Create as PayrollCreate;
use App\Livewire\Modules\Finances\Payrolls\Index as PayrollIndex;
use App\Livewire\Modules\Finances\Payrolls\Show as PayrollShow;
use App\Livewire\Modules\Finances\Payrolls\Stats as PayrollStats;
use App\Livewire\Modules\Finances\Payrolls\Update as PayrollUpdate;

use App\Livewire\Modules\Finances\Invoices\Index as InvoicesIndex;
use App\Livewire\Modules\Finances\Invoices\Clients\Index as InvoicesClientsIndex;
use App\Livewire\Modules\Finances\Invoices\Clients\Create as InvoicesClientsCreate;
use App\Livewire\Modules\Finances\Invoices\Clients\Update as InvoicesClientsUpdate;
use App\Livewire\Modules\Finances\Invoices\Clients\Show as InvoicesClientsShow;

use App\Livewire\Modules\Finances\Invoices\Providers\Index as InvoicesProvidersIndex;
use App\Livewire\Modules\Finances\Invoices\Providers\Create as InvoicesProvidersCreate;
use App\Livewire\Modules\Finances\Invoices\Providers\Update as InvoicesProvidersUpdate;
use App\Livewire\Modules\Finances\Invoices\Providers\Show as InvoicesProvidersShow;

use App\Livewire\Modules\Finances\Invoices\Clients\BillingAccounts\Index as InvoicesClientsBillingAccountsIndex;
use App\Livewire\Modules\Finances\Invoices\Clients\BillingAccounts\Create as InvoicesClientsBillingAccountsCreate;
use App\Livewire\Modules\Finances\Invoices\Clients\BillingAccounts\Update as InvoicesClientsBillingAccountsUpdate;
use App\Livewire\Modules\Finances\Invoices\Clients\BillingAccounts\Show as InvoicesClientsBillingAccountsShow;


use App\Livewire\Modules\Finances\Norms\Index as NormsIndex;
use App\Livewire\Modules\Finances\Norms\Create as NormsCreate;
use App\Livewire\Modules\Finances\Norms\Update as NormsUpdate;
use App\Livewire\Modules\Finances\Norms\Show as NormsShow;

use App\Livewire\Modules\Finances\Audits\Index as AuditsIndex;
use App\Livewire\Modules\Finances\Audits\Create as AuditsCreate;
use App\Livewire\Modules\Finances\Audits\Update as AuditsUpdate;
use App\Livewire\Modules\Finances\Audits\Show as AuditsShow;


use App\Livewire\Modules\Taxes\Create as TaxCreate;
use App\Livewire\Modules\Taxes\Index as TaxIndex;
use App\Livewire\Modules\Taxes\Show as TaxShow;
use App\Livewire\Modules\Taxes\Update as TaxUpdate;
use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,finanzas")
    ->prefix("finances")
    ->name("finances.")
    ->group(function () {
        Route::get("/", Index::class)->name("index");
        // ->middleware("can-area:view,finanzas");
    
        // Route::get('/{caseRecord}/edit', Update::class)->name('edit');
    
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get("/", InvoicesIndex::class)->name("index");

            Route::prefix('clients')->name('clients.')->group(function () {
                Route::get("/", InvoicesClientsIndex::class)->name("index");
                Route::get("/create", InvoicesClientsCreate::class)->name("create");

                // Billing accounts routes must come BEFORE wildcard routes to prevent route conflicts
                Route::prefix('billing-accounts')->name('billing-accounts.')->group(function () {
                    Route::get("/", InvoicesClientsBillingAccountsIndex::class)->name("index");
                    Route::get("/create", InvoicesClientsBillingAccountsCreate::class)->name("create");
                    Route::get("/{billingAccount}/edit", InvoicesClientsBillingAccountsUpdate::class)->name("edit");
                    Route::get("/{billingAccount}", InvoicesClientsBillingAccountsShow::class)->name("show");
                });

                // Wildcard routes should come last to prevent conflicts
                Route::get("/{invoiceClient}/edit", InvoicesClientsUpdate::class)->name("edit");
                Route::get("/{invoiceClient}", InvoicesClientsShow::class)->name("show");
            });

            Route::prefix('providers')->name('providers.')->group(function () {
                Route::get("/", InvoicesProvidersIndex::class)->name("index");
                Route::get("/create", InvoicesProvidersCreate::class)->name("create");
                Route::get("/{invoiceProvider}/edit", InvoicesProvidersUpdate::class)->name("edit");
                Route::get("/{invoiceProvider}", InvoicesProvidersShow::class)->name("show");
            });

        });


        Route::prefix("taxes")->name("taxes.")->group(function () {
            Route::get("/", TaxIndex::class)->name("index");
            Route::get("/create", TaxCreate::class)->name("create");
            Route::get("/{taxRecord}/edit", TaxUpdate::class)->name("edit");
            Route::get("/{taxRecord}", TaxShow::class)->name("show");
        });


        Route::prefix("payrolls")->name("payrolls.")->group(function () {
            Route::get("/", PayrollIndex::class)->name("index");
            Route::get("/stats", PayrollStats::class)->name("stats");
            Route::get("/create", PayrollCreate::class)->name("create");
            Route::get("/{payroll}/edit", PayrollUpdate::class)->name("edit");
            Route::get("/{payroll}", PayrollShow::class)->name("show");
        });

        Route::prefix("net")->name("net.")->group(function () {
            Route::get("/", NetIndex::class)->name("index");
        });


        Route::prefix("gross")->name("gross.")->group(function () {
            Route::get("/", GrossIndex::class)->name("index");
            Route::get("/edit/{income}", \App\Livewire\Modules\Finances\Gross\Components\CreateOrUpdate::class)->name("edit");
            Route::get("/create", \App\Livewire\Modules\Finances\Gross\Components\CreateOrUpdate::class)->name("create");
        });

        Route::prefix("expenses")->name("expenses.")->group(function () {
            Route::get("/", ExpenseIndex::class)->name("index");
            Route::get("/edit/{expense}", \App\Livewire\Modules\Finances\Expense\Components\CreateOrUpdate::class)->name("edit");
            Route::get("/create", \App\Livewire\Modules\Finances\Expense\Components\CreateOrUpdate::class)->name("create");
        });

        Route::prefix("norms")->name("norms.")->group(function () {
            Route::get("/", NormsIndex::class)->name("index");
            Route::get("/create", NormsCreate::class)->name("create");
            Route::get("/{norm}/edit", NormsUpdate::class)->name("edit");
            Route::get("/{norm}", NormsShow::class)->name("show");
        });

        Route::prefix("audits")->name("audits.")->group(function () {
            Route::get("/", AuditsIndex::class)->name("index");
            Route::get("/create", AuditsCreate::class)->name("create");
            Route::get("/{audit}/edit", AuditsUpdate::class)->name("edit");
            Route::get("/{audit}", AuditsShow::class)->name("show");
        });
    });
