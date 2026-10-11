<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;

Schedule::command('eims:birthday-reminders')->dailyAt('08:00');
Schedule::command('eims:expiry-reminders')->dailyAt('08:30');
Schedule::command('leaves:accrue-monthly')->monthlyOn(1, '00:00');
Schedule::command('attendance:autopunchout')->dailyAt('21:00');
Schedule::command('attendance:autopunchout')->everyThirtyMinutes();

