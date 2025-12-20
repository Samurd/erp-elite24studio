<?php

// use App\Livewire\Modules\Finances\Expense\Index as ExpenseIndex;
// use App\Livewire\Modules\Finances\Gross\Index as GrossIndex;
// use App\Livewire\Modules\Finances\Index;

use App\Livewire\Modules\Rrhh\Index;
use App\Livewire\Modules\Rrhh\Contracts\Index as ContractsIndex;
use App\Livewire\Modules\Rrhh\Contracts\Create as ContractsCreate;
use App\Livewire\Modules\Rrhh\Contracts\Update as ContractsUpdate;
use App\Livewire\Modules\Rrhh\Contracts\Show as ContractsShow;

use App\Livewire\Modules\Rrhh\Employees\Index as EmployeesIndex;
use App\Livewire\Modules\Rrhh\Employees\Create as EmployeesCreate;
use App\Livewire\Modules\Rrhh\Employees\Update as EmployeesUpdate;

use App\Livewire\Modules\Rrhh\Vacancies\Index as VacanciesIndex;
use App\Livewire\Modules\Rrhh\Vacancies\Create as VacanciesCreate;
use App\Livewire\Modules\Rrhh\Vacancies\Update as VacanciesUpdate;

use App\Livewire\Modules\Rrhh\Applicants\Create as ApplicantsCreate;
use App\Livewire\Modules\Rrhh\Applicants\Update as ApplicantsUpdate;

use App\Livewire\Modules\Rrhh\Interviews\Index as InterviewsIndex;
use App\Livewire\Modules\Rrhh\Interviews\Create as InterviewsCreate;
use App\Livewire\Modules\Rrhh\Interviews\Update as InterviewsUpdate;
use App\Livewire\Modules\Rrhh\Interviews\Calendar\Index as InterviewsCalendarIndex;

use App\Livewire\Modules\Rrhh\Inductions\Index as InductionsIndex;
use App\Livewire\Modules\Rrhh\Inductions\Create as InductionsCreate;
use App\Livewire\Modules\Rrhh\Inductions\Update as InductionsUpdate;

use App\Livewire\Modules\Rrhh\Kits\Index as KitsIndex;
use App\Livewire\Modules\Rrhh\Kits\Create as KitsCreate;
use App\Livewire\Modules\Rrhh\Kits\Update as KitsUpdate;

use App\Livewire\Modules\Rrhh\Birthdays\Index as BirthdaysIndex;
use App\Livewire\Modules\Rrhh\Birthdays\Create as BirthdaysCreate;
use App\Livewire\Modules\Rrhh\Birthdays\Update as BirthdaysUpdate;
use App\Livewire\Modules\Rrhh\Birthdays\Show as BirthdaysShow;

use App\Livewire\Modules\Rrhh\OffBoardings\Index as OffBoardingsIndex;
use App\Livewire\Modules\Rrhh\OffBoardings\Create as OffBoardingsCreate;
use App\Livewire\Modules\Rrhh\OffBoardings\Update as OffBoardingsUpdate;
use App\Livewire\Modules\Rrhh\OffBoardings\Show as OffBoardingsShow;

use App\Livewire\Modules\Rrhh\Attendances\Index as AttendancesIndex;
use App\Livewire\Modules\Rrhh\Attendances\Create as AttendancesCreate;
use App\Livewire\Modules\Rrhh\Attendances\Update as AttendancesUpdate;
use App\Livewire\Modules\Rrhh\Attendances\Show as AttendancesShow;

use App\Livewire\Modules\Rrhh\Holidays\Index as HolidaysIndex;
use App\Livewire\Modules\Rrhh\Holidays\Create as HolidaysCreate;
use App\Livewire\Modules\Rrhh\Holidays\Update as HolidaysUpdate;
use App\Livewire\Modules\Rrhh\Holidays\Show as HolidaysShow;

use Illuminate\Support\Facades\Route;

Route::middleware('can-area:view,finanzas')
    ->prefix('rrhh')
    ->name('rrhh.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Modules\RrhhController::class, 'index'])->name('index');

        Route::prefix('contracts')->name('contracts.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Modules\RrhhContractsController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Modules\RrhhContractsController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Modules\RrhhContractsController::class, 'store'])->name('store');
            Route::get('/{contract}/edit', [\App\Http\Controllers\Modules\RrhhContractsController::class, 'edit'])->name('edit');
            Route::put('/{contract}', [\App\Http\Controllers\Modules\RrhhContractsController::class, 'update'])->name('update');
            Route::get('/{contract}/show', [\App\Http\Controllers\Modules\RrhhContractsController::class, 'show'])->name('show');
            Route::delete('/{contract}', [\App\Http\Controllers\Modules\RrhhContractsController::class, 'destroy'])->name('destroy');
        });


        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Modules\RrhhEmployeesController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Modules\RrhhEmployeesController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Modules\RrhhEmployeesController::class, 'store'])->name('store');
            Route::get('/{employee}/edit', [\App\Http\Controllers\Modules\RrhhEmployeesController::class, 'edit'])->name('edit');
            Route::put('/{employee}', [\App\Http\Controllers\Modules\RrhhEmployeesController::class, 'update'])->name('update');
            Route::delete('/{employee}', [\App\Http\Controllers\Modules\RrhhEmployeesController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('vacancies')->name('vacancies.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Modules\RrhhVacanciesController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Modules\RrhhVacanciesController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Modules\RrhhVacanciesController::class, 'store'])->name('store');
            Route::get('/{vacancy}/edit', [\App\Http\Controllers\Modules\RrhhVacanciesController::class, 'edit'])->name('edit');
            Route::put('/{vacancy}', [\App\Http\Controllers\Modules\RrhhVacanciesController::class, 'update'])->name('update');
            Route::delete('/{vacancy}', [\App\Http\Controllers\Modules\RrhhVacanciesController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('applicants')->name('applicants.')->group(function () {
            Route::get('/create', ApplicantsCreate::class)->name('create');
            Route::get('/{applicant}/edit', ApplicantsUpdate::class)->name('edit');
        });

        Route::prefix('interviews')->name('interviews.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Modules\RrhhInterviewsController::class, 'index'])->name('index');
            Route::get('/calendar', [\App\Http\Controllers\Modules\RrhhInterviewsController::class, 'calendar'])->name('calendar');
            Route::get('/create', [\App\Http\Controllers\Modules\RrhhInterviewsController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Modules\RrhhInterviewsController::class, 'store'])->name('store');
            Route::get('/{interview}/edit', [\App\Http\Controllers\Modules\RrhhInterviewsController::class, 'edit'])->name('edit');
            Route::put('/{interview}', [\App\Http\Controllers\Modules\RrhhInterviewsController::class, 'update'])->name('update');
            Route::delete('/{interview}', [\App\Http\Controllers\Modules\RrhhInterviewsController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('inductions')->name('inductions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Modules\RrhhInductionsController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Modules\RrhhInductionsController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Modules\RrhhInductionsController::class, 'store'])->name('store');
            Route::get('/{induction}/edit', [\App\Http\Controllers\Modules\RrhhInductionsController::class, 'edit'])->name('edit');
            Route::put('/{induction}', [\App\Http\Controllers\Modules\RrhhInductionsController::class, 'update'])->name('update');
            Route::delete('/{induction}', [\App\Http\Controllers\Modules\RrhhInductionsController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('kits')->name('kits.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Modules\RrhhKitsController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Modules\RrhhKitsController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Modules\RrhhKitsController::class, 'store'])->name('store');
            Route::get('/{kit}/edit', [\App\Http\Controllers\Modules\RrhhKitsController::class, 'edit'])->name('edit');
            Route::put('/{kit}', [\App\Http\Controllers\Modules\RrhhKitsController::class, 'update'])->name('update');
            Route::delete('/{kit}', [\App\Http\Controllers\Modules\RrhhKitsController::class, 'destroy'])->name('destroy');
        });


        Route::prefix('birthdays')->name('birthdays.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Modules\RrhhBirthdaysController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Modules\RrhhBirthdaysController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Modules\RrhhBirthdaysController::class, 'store'])->name('store');
            Route::get('/{birthday}/edit', [\App\Http\Controllers\Modules\RrhhBirthdaysController::class, 'edit'])->name('edit');
            Route::put('/{birthday}', [\App\Http\Controllers\Modules\RrhhBirthdaysController::class, 'update'])->name('update');
            Route::delete('/{birthday}', [\App\Http\Controllers\Modules\RrhhBirthdaysController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('offboardings')->name('offboardings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Modules\RrhhOffboardingsController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Modules\RrhhOffboardingsController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Modules\RrhhOffboardingsController::class, 'store'])->name('store');
            Route::get('/{offboarding}', [\App\Http\Controllers\Modules\RrhhOffboardingsController::class, 'show'])->name('show');
            Route::get('/{offboarding}/edit', [\App\Http\Controllers\Modules\RrhhOffboardingsController::class, 'edit'])->name('edit');
            Route::put('/{offboarding}', [\App\Http\Controllers\Modules\RrhhOffboardingsController::class, 'update'])->name('update');
            Route::delete('/{offboarding}', [\App\Http\Controllers\Modules\RrhhOffboardingsController::class, 'destroy'])->name('destroy');

            // Tasks routes
            Route::post('/{offboarding}/tasks', [\App\Http\Controllers\Modules\RrhhOffboardingsController::class, 'addTask'])->name('tasks.store');
            Route::put('/tasks/{task}/toggle', [\App\Http\Controllers\Modules\RrhhOffboardingsController::class, 'toggleTask'])->name('tasks.toggle');
            Route::delete('/tasks/{task}', [\App\Http\Controllers\Modules\RrhhOffboardingsController::class, 'deleteTask'])->name('tasks.destroy');
        });

        Route::prefix('attendances')->name('attendances.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Modules\RrhhAttendancesController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Modules\RrhhAttendancesController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Modules\RrhhAttendancesController::class, 'store'])->name('store');
            Route::get('/{attendance}/edit', [\App\Http\Controllers\Modules\RrhhAttendancesController::class, 'edit'])->name('edit');
            Route::put('/{attendance}', [\App\Http\Controllers\Modules\RrhhAttendancesController::class, 'update'])->name('update');
            Route::delete('/{attendance}', [\App\Http\Controllers\Modules\RrhhAttendancesController::class, 'destroy'])->name('destroy');
        });


        Route::prefix('holidays')->name('holidays.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Modules\RrhhHolidaysController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Modules\RrhhHolidaysController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Modules\RrhhHolidaysController::class, 'store'])->name('store');
            Route::get('/{holiday}/edit', [\App\Http\Controllers\Modules\RrhhHolidaysController::class, 'edit'])->name('edit');
            Route::put('/{holiday}', [\App\Http\Controllers\Modules\RrhhHolidaysController::class, 'update'])->name('update');
            Route::delete('/{holiday}', [\App\Http\Controllers\Modules\RrhhHolidaysController::class, 'destroy'])->name('destroy');
            Route::get('/{holiday}', [\App\Http\Controllers\Modules\RrhhHolidaysController::class, 'show'])->name('show');
        });
    });
