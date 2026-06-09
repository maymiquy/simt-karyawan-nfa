<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cek dan tandai tugas overdue setiap hari jam 00:05
Schedule::command('tasks:check-overdue')->dailyAt('00:05');

// Notifikasi tenggat < 24 jam, dicek tiap jam (anti-duplikat di dalam command)
Schedule::command('tasks:notify-deadlines')->hourly();
