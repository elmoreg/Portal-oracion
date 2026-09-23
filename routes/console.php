<?php

use App\Console\Commands\AssignPrayerRequests;
use Illuminate\Support\Facades\Schedule;

Schedule::command(AssignPrayerRequests::class)->hourly()->withoutOverlapping();
