<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automated Movie Context Pipeline
Schedule::command('scrape:sources')
    ->dailyAt('23:00')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('content:select')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('content:generate')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->runInBackground();

// Weekly cleanup
Schedule::command('content:cleanup-old-scraped')
    ->weeklyOn(0, '03:00') // Sunday at 3:00
    ->withoutOverlapping()
    ->runInBackground();

// Daily sitemap generation
Schedule::command('sitemap:generate')
    ->daily()
    ->withoutOverlapping()
    ->runInBackground();
