<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reset monthly credits and apply rollover on the 1st of every month at 2:00 AM
Schedule::command('billing:monthly-rollover')->monthlyOn(1, '02:00');
