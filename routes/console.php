<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programación de notificaciones
Schedule::command('notifications:send-scheduled')->everyMinute();
Schedule::command('notifications:send-recurring')->everyMinute();
Schedule::command('notifications:send-reminders')->dailyAt('07:00');
