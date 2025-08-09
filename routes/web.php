<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AppointmentController;
use App\Services\SmsService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Standart Auth rotalarını ekler (login, register, logout vb.)
Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');

// Rezervasyon API rotaları
Route::get('/tarihler/{sport_id}', [ReservationController::class, 'getDates']);
Route::get('/saatler/{sport_id}/{tarih}', [ReservationController::class, 'getHours']);

// Sadece giriş yapmış kullanıcıların erişebileceği rota
Route::post('/randevu-al', [ReservationController::class, 'makeReservation'])->middleware('auth')->name('reservation.make');

Route::post('/randevu/kimlik-dogrula', [AppointmentController::class, 'verifyIdentityAndSendSms']);
Route::post('/randevu/onayla', [AppointmentController::class, 'confirmAppointment']);

// Randevu İptal Rotaları
Route::post('/randevu/sorgula', [AppointmentController::class, 'findAppointment']);
Route::post('/randevu/iptal-et', [AppointmentController::class, 'cancelAppointment']);


Route::get('/sms-test-variants', function (Request $request, SmsService $smsService) {
    $phone = $request->query('phone', '5062996463'); // senin verdiğin 10 haneli
    $msg = $request->query('message', 'Deneme kodu: 123456');
    $otp = $request->query('otp', '1') === '1';
    $res = $smsService->sendSmsVariants($phone, $msg, $otp);
    return response()->json($res);
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // YENİ EKLENEN RANDEVU İŞLEM ROTALARI
    Route::get('/appointments/{id}', [DashboardController::class, 'show'])->name('appointments.show');
    Route::put('/appointments/{id}', [DashboardController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{id}', [DashboardController::class, 'destroy'])->name('appointments.destroy');

    Route::post('/appointments/{id}/mark-as-no-show', [DashboardController::class, 'markAsNoShow'])->name('appointments.noshow');

    Route::post('/appointments/{id}/confirm-attendance', [DashboardController::class, 'confirmAttendance'])->name('appointments.confirm_attendance');


    Route::resource('announcements', AnnouncementController::class);

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'store'])->name('settings.store');

    Route::resource('notes', NoteController::class); // Notlar için resource route


    // -- AYARLAR VE TESİS YÖNETİMİ ROTALARI --
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    // Genel Ayarları Güncelleme
    Route::post('settings/general', [SettingController::class, 'updateGeneralSettings'])->name('settings.general.update');
    // Spor Tesisleri Yönetimi
    Route::post('sports', [SettingController::class, 'storeSport'])->name('sports.store');
    Route::put('sports/{sport}', [SettingController::class, 'updateSport'])->name('sports.update');


});