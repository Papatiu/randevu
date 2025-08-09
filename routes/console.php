<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\UpdatePastAppointmentsStatus;
use App\Jobs\CreateNewSlots;
use App\Jobs\PruneOldSlots;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// --- HATALI KODUN YERİNE DOĞRU KOD ---
// Laravel 12'de 'inspire' komutu bu şekilde tanımlanır.
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
|--------------------------------------------------------------------------
| Command Schedule
|--------------------------------------------------------------------------
*/

// --- GÖREV 1: ESKİ SLOTLARI TEMİZLEME ---
Schedule::job(new PruneOldSlots)->dailyAt('00:30');

// --- GÖREV 2: YENİ SLOTLARI OLUŞTURMA ---
Schedule::job(new CreateNewSlots)->dailyAt('15:01');

// --- GÖREV 3: GEÇMİŞ RANDEVULARI GÜNCELLEME ---
Schedule::job(new UpdatePastAppointmentsStatus)->hourly();

// --- TEST AMAÇLI ZAMANLAMA ---
// Schedule::job(new PruneOldSlots)->everyMinute();
// Schedule::job(new CreateNewSlots)->everyMinute();
// Schedule::job(new UpdatePastAppointmentsStatus)->everyMinute();