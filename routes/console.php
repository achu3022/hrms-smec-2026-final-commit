<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;

Schedule::command('eims:birthday-reminders')->dailyAt('08:00');
Schedule::command('eims:expiry-reminders')->dailyAt('08:30');
