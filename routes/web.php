<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Auth;

// Standart Auth rotalarını ekler (login, register, logout vb.)
Auth::routes(); 

Route::get('/', [HomeController::class, 'index'])->name('home');

// Rezervasyon API rotaları
Route::get('/tarihler/{sport_id}', [ReservationController::class, 'getDates']);
Route::get('/saatler/{sport_id}/{tarih}', [ReservationController::class, 'getHours']);

// Sadece giriş yapmış kullanıcıların erişebileceği rota
Route::post('/randevu-al', [ReservationController::class, 'makeReservation'])->middleware('auth')->name('reservation.make');