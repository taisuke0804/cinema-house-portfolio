<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('screenings:delete-past')
    ->dailyAt('03:00')
    ->timezone('Asia/Tokyo')
    ->withoutOverlapping() // タスク多重起動の防止
    ->evenInMaintenanceMode(); // メンテ中も実行
