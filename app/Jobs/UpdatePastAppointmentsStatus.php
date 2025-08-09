<?php

namespace App\Jobs;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Loglama için

class UpdatePastAppointmentsStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Şu anki zamanı al
        $now = Carbon::now();

        // Durumu "onaylandi" olan ve tarihi geçmiş randevuları bul
        // Not: Saat formatı "18:00 - 19:00" olduğu için sadece bitiş saatini kontrol etmeliyiz.
        $pastAppointments = Appointment::where('durum', 'onaylandi')
            ->whereHas('slot', function ($query) use ($now) {
                // Önce tarihe göre filtrele
                $query->where('tarih', '<', $now->toDateString())
                      // Veya tarihi bugün olup saati geçmiş olanları bul
                      ->orWhere(function ($query) use ($now) {
                          $query->where('tarih', '=', $now->toDateString())
                                // saat sütunundan sadece bitiş saatini alıp (örn: 19:00) kontrol ediyoruz.
                                ->where(DB::raw("SUBSTRING_INDEX(saat, ' - ', -1)"), '<', $now->toTimeString());
                      });
            })->get();

        $updatedCount = 0;
        foreach ($pastAppointments as $appointment) {
            $appointment->durum = 'tamamlandi';
            $appointment->aciklama = 'Sistem tarafından otomatik olarak tamamlandı.';
            $appointment->save();
            $updatedCount++;
        }

        if ($updatedCount > 0) {
            // Ne yaptığımızı log dosyasına yazalım. Bu, hata ayıklama için çok faydalıdır.
            Log::info("{$updatedCount} adet geçmiş randevu 'tamamlandı' olarak güncellendi.");
        }
    }
}