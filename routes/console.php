<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // Schedule Facade'ını ekle

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// YENİ EKLENECEK ZAMANLAMA KODU BURASI
Schedule::command('app:create-daily-slots')->dailyAt('02:00');