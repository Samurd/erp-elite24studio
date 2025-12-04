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
        Route::get('/', Index::class)->name('index');

        Route::prefix('contracts')->name('contracts.')->group(function () {
            Route::get('/', ContractsIndex::class)->name('index');
            Route::get('/create', ContractsCreate::class)->name('create');
            Route::get('/{contract}/edit', ContractsUpdate::class)->name('edit');
            Route::get('/{contract}/show', ContractsShow::class)->name('show');
        });


        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', EmployeesIndex::class)->name('index');
            Route::get('/create', EmployeesCreate::class)->name('create');
            Route::get('/{employee}/edit', EmployeesUpdate::class)->name('edit');
        });

        Route::prefix('vacancies')->name('vacancies.')->group(function () {
            Route::get('/', VacanciesIndex::class)->name('index');
            Route::get('/create', VacanciesCreate::class)->name('create');
            Route::get('/{vacancy}/edit', VacanciesUpdate::class)->name('edit');
        });
        Route::prefix('applicants')->name('applicants.')->group(function () {
            Route::get('/create', ApplicantsCreate::class)->name('create');
            Route::get('/{applicant}/edit', ApplicantsUpdate::class)->name('edit');
        });

        Route::prefix('interviews')->name('interviews.')->group(function () {
            Route::get('/', InterviewsIndex::class)->name('index');
            Route::get('/create', InterviewsCreate::class)->name('create');
            Route::get('/{interview}/edit', InterviewsUpdate::class)->name('edit');
            Route::get('/calendar', InterviewsCalendarIndex::class)->name('calendar');
        });

        Route::prefix('inductions')->name('inductions.')->group(function () {
            Route::get('/', InductionsIndex::class)->name('index');
            Route::get('/create', InductionsCreate::class)->name('create');
            Route::get('/{induction}/edit', InductionsUpdate::class)->name('edit');
        });

        Route::prefix('kits')->name('kits.')->group(function () {
            Route::get('/', KitsIndex::class)->name('index');
            Route::get('/create', KitsCreate::class)->name('create');
            Route::get('/{kit}/edit', KitsUpdate::class)->name('edit');
        });


        Route::prefix('birthdays')->name('birthdays.')->group(function () {
            Route::get('/', BirthdaysIndex::class)->name('index');
            Route::get('/create', BirthdaysCreate::class)->name('create');
            Route::get('/{birthday}/edit', BirthdaysUpdate::class)->name('edit');
            Route::get('/{birthday}/show', BirthdaysShow::class)->name('show');
        });

        Route::prefix('offboardings')->name('offboardings.')->group(function () {
            Route::get('/', OffBoardingsIndex::class)->name('index');
            Route::get('/create', OffBoardingsCreate::class)->name('create');
            Route::get('/{offboarding}/edit', OffBoardingsUpdate::class)->name('edit');
            Route::get('/{offboarding}/show', OffBoardingsShow::class)->name('show');
        });

        Route::prefix('attendances')->name('attendances.')->group(function () {
            Route::get('/', AttendancesIndex::class)->name('index');
            Route::get('/create', AttendancesCreate::class)->name('create');
            Route::get('/{attendance}/edit', AttendancesUpdate::class)->name('edit');
            Route::get('/{attendance}/show', AttendancesShow::class)->name('show');
        });


        Route::prefix('holidays')->name('holidays.')->group(function () {
            Route::get('/', HolidaysIndex::class)->name('index');
            Route::get('/create', HolidaysCreate::class)->name('create');
            Route::get('/{holiday}/edit', HolidaysUpdate::class)->name('edit');
            Route::get('/{holiday}/show', HolidaysShow::class)->name('show');
        });
    });
