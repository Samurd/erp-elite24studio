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
        Route::get("/", [\App\Http\Controllers\Modules\FinancesController::class, 'index'])->name("index");
        // ->middleware("can-area:view,finanzas");
    
        // Route::get('/{caseRecord}/edit', Update::class)->name('edit');
    
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get("/", [\App\Http\Controllers\Modules\InvoicesController::class, 'index'])->name("index");

            Route::prefix('clients')->name('clients.')->group(function () {
                Route::get("/", [\App\Http\Controllers\Modules\Invoices\ClientsController::class, 'index'])->name("index");
                Route::get("/create", [\App\Http\Controllers\Modules\Invoices\ClientsController::class, 'create'])->name("create");
                Route::post("/", [\App\Http\Controllers\Modules\Invoices\ClientsController::class, 'store'])->name("store");

                // Billing accounts routes must come BEFORE wildcard routes to prevent route conflicts
                Route::prefix('billing-accounts')->name('billing-accounts.')->group(function () {
                    Route::get("/", [\App\Http\Controllers\Modules\Invoices\Clients\BillingAccountsController::class, 'index'])->name("index");
                    Route::get("/create", [\App\Http\Controllers\Modules\Invoices\Clients\BillingAccountsController::class, 'create'])->name("create");
                    Route::post("/", [\App\Http\Controllers\Modules\Invoices\Clients\BillingAccountsController::class, 'store'])->name("store");
                    Route::get("/{billingAccount}/edit", [\App\Http\Controllers\Modules\Invoices\Clients\BillingAccountsController::class, 'edit'])->name("edit");
                    Route::post("/{billingAccount}", [\App\Http\Controllers\Modules\Invoices\Clients\BillingAccountsController::class, 'update'])->name("update");
                    Route::get("/{billingAccount}", [\App\Http\Controllers\Modules\Invoices\Clients\BillingAccountsController::class, 'show'])->name("show");
                    Route::delete("/{billingAccount}", [\App\Http\Controllers\Modules\Invoices\Clients\BillingAccountsController::class, 'destroy'])->name("destroy");
                });

                // Wildcard routes should come last to prevent conflicts
                Route::get("/{invoiceClient}/edit", [\App\Http\Controllers\Modules\Invoices\ClientsController::class, 'edit'])->name("edit");
                Route::post("/{invoiceClient}", [\App\Http\Controllers\Modules\Invoices\ClientsController::class, 'update'])->name("update");
                Route::get("/{invoiceClient}", [\App\Http\Controllers\Modules\Invoices\ClientsController::class, 'show'])->name("show");
                Route::delete("/{invoiceClient}", [\App\Http\Controllers\Modules\Invoices\ClientsController::class, 'destroy'])->name("destroy");
            });

            Route::prefix('providers')->name('providers.')->group(function () {
                Route::get("/", [\App\Http\Controllers\Modules\Invoices\ProvidersController::class, 'index'])->name("index");
                Route::get("/create", [\App\Http\Controllers\Modules\Invoices\ProvidersController::class, 'create'])->name("create");
                Route::post("/", [\App\Http\Controllers\Modules\Invoices\ProvidersController::class, 'store'])->name("store");
                Route::get("/{invoiceProvider}/edit", [\App\Http\Controllers\Modules\Invoices\ProvidersController::class, 'edit'])->name("edit");
                Route::post("/{invoiceProvider}", [\App\Http\Controllers\Modules\Invoices\ProvidersController::class, 'update'])->name("update");
                Route::get("/{invoiceProvider}", [\App\Http\Controllers\Modules\Invoices\ProvidersController::class, 'show'])->name("show");
                Route::delete("/{invoiceProvider}", [\App\Http\Controllers\Modules\Invoices\ProvidersController::class, 'destroy'])->name("destroy");
            });

        });


        Route::prefix("taxes")->name("taxes.")->group(function () {
            Route::get("/", [\App\Http\Controllers\Modules\TaxesController::class, 'index'])->name("index");
            Route::get("/create", [\App\Http\Controllers\Modules\TaxesController::class, 'create'])->name("create");
            Route::post("/", [\App\Http\Controllers\Modules\TaxesController::class, 'store'])->name("store");
            Route::get("/{taxRecord}/edit", [\App\Http\Controllers\Modules\TaxesController::class, 'edit'])->name("edit");
            Route::post("/{taxRecord}", [\App\Http\Controllers\Modules\TaxesController::class, 'update'])->name("update"); // Using POST for file upload spoofing
            Route::get("/{taxRecord}", [\App\Http\Controllers\Modules\TaxesController::class, 'show'])->name("show");
            Route::delete("/{taxRecord}", [\App\Http\Controllers\Modules\TaxesController::class, 'destroy'])->name("destroy");
        });


        Route::prefix("payrolls")->name("payrolls.")->group(function () {
            Route::get("/", [\App\Http\Controllers\Modules\PayrollsController::class, 'index'])->name("index");
            Route::get("/stats", [\App\Http\Controllers\Modules\PayrollsController::class, 'stats'])->name("stats");
            Route::get("/create", [\App\Http\Controllers\Modules\PayrollsController::class, 'create'])->name("create");
            Route::post("/", [\App\Http\Controllers\Modules\PayrollsController::class, 'store'])->name("store");
            Route::get("/{payroll}/edit", [\App\Http\Controllers\Modules\PayrollsController::class, 'edit'])->name("edit");
            Route::post("/{payroll}", [\App\Http\Controllers\Modules\PayrollsController::class, 'update'])->name("update"); // Using POST for file upload spoofing
            Route::get("/{payroll}", [\App\Http\Controllers\Modules\PayrollsController::class, 'show'])->name("show");
            Route::delete("/{payroll}", [\App\Http\Controllers\Modules\PayrollsController::class, 'destroy'])->name("destroy");
        });

        Route::prefix("net")->name("net.")->group(function () {
            Route::get("/", [\App\Http\Controllers\Modules\NetIncomeController::class, 'index'])->name("index");
        });


        Route::prefix("gross")->name("gross.")->group(function () {
            Route::get("/", [\App\Http\Controllers\Modules\GrossIncomeController::class, 'index'])->name("index");
            Route::get("/create", [\App\Http\Controllers\Modules\GrossIncomeController::class, 'create'])->name("create");
            Route::post("/", [\App\Http\Controllers\Modules\GrossIncomeController::class, 'store'])->name("store");
            Route::get("/{income}/edit", [\App\Http\Controllers\Modules\GrossIncomeController::class, 'edit'])->name("edit");
            Route::put("/{income}", [\App\Http\Controllers\Modules\GrossIncomeController::class, 'update'])->name("update");
            Route::delete("/{income}", [\App\Http\Controllers\Modules\GrossIncomeController::class, 'destroy'])->name("destroy");
        });

        Route::prefix("expenses")->name("expenses.")->group(function () {
            Route::get("/", [\App\Http\Controllers\Modules\ExpensesController::class, 'index'])->name("index");
            Route::get("/create", [\App\Http\Controllers\Modules\ExpensesController::class, 'create'])->name("create");
            Route::post("/", [\App\Http\Controllers\Modules\ExpensesController::class, 'store'])->name("store");
            Route::get("/{expense}/edit", [\App\Http\Controllers\Modules\ExpensesController::class, 'edit'])->name("edit");
            Route::put("/{expense}", [\App\Http\Controllers\Modules\ExpensesController::class, 'update'])->name("update");
            Route::delete("/{expense}", [\App\Http\Controllers\Modules\ExpensesController::class, 'destroy'])->name("destroy");
        });

        Route::prefix("norms")->name("norms.")->group(function () {
            Route::get("/", [\App\Http\Controllers\Modules\NormsController::class, 'index'])->name("index");
            Route::get("/create", [\App\Http\Controllers\Modules\NormsController::class, 'create'])->name("create");
            Route::post("/", [\App\Http\Controllers\Modules\NormsController::class, 'store'])->name("store");
            Route::get("/{norm}/edit", [\App\Http\Controllers\Modules\NormsController::class, 'edit'])->name("edit");
            Route::post("/{norm}", [\App\Http\Controllers\Modules\NormsController::class, 'update'])->name("update"); // Using POST for file upload spoofing
            Route::get("/{norm}", [\App\Http\Controllers\Modules\NormsController::class, 'show'])->name("show");
            Route::delete("/{norm}", [\App\Http\Controllers\Modules\NormsController::class, 'destroy'])->name("destroy");
        });

        Route::prefix("audits")->name("audits.")->group(function () {
            Route::get("/", [\App\Http\Controllers\Modules\AuditsController::class, 'index'])->name("index");
            Route::get("/create", [\App\Http\Controllers\Modules\AuditsController::class, 'create'])->name("create");
            Route::post("/", [\App\Http\Controllers\Modules\AuditsController::class, 'store'])->name("store");
            Route::get("/{audit}/edit", [\App\Http\Controllers\Modules\AuditsController::class, 'edit'])->name("edit");
            Route::post("/{audit}", [\App\Http\Controllers\Modules\AuditsController::class, 'update'])->name("update"); // Using POST for file upload spoofing
            Route::get("/{audit}", [\App\Http\Controllers\Modules\AuditsController::class, 'show'])->name("show");
            Route::delete("/{audit}", [\App\Http\Controllers\Modules\AuditsController::class, 'destroy'])->name("destroy");
        });
    });
