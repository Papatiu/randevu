<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Sport;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CreateNewSlots implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct() {}

    public function handle(): void
    {
        $sports = Sport::all();
        $startHour = Carbon::createFromTime(18, 0, 0);
        $endHour = Carbon::createFromTime(23, 59, 0);
        
        // Veritabanındaki en ileri tarihli slot hangi gün?
        $lastSlotDate = Slot::max('tarih');
        
        // Eğer hiç slot yoksa, bugünden başla. Varsa, son günden bir sonraki günden başla.
        $startDate = $lastSlotDate ? Carbon::parse($lastSlotDate)->addDay() : Carbon::now();

        // Her zaman 7 gün ilerisini hedefle
        $endDate = Carbon::now()->addDays(7);
        
        $createdCount = 0;

        // Eksik günleri tamamla
        for ($date = $startDate; $date->lessThanOrEqualTo($endDate); $date->addDay()) {
            foreach ($sports as $sport) {
                $currentHour = clone $startHour;

                while ($currentHour->lessThanOrEqualTo($endHour)) {
                    $nextHour = clone $currentHour;
                    $nextHour->addHour();
                    
                    $slotTime = $currentHour->format('H:i') . ' - ' . $nextHour->format('H:i');

                    Slot::firstOrCreate(
                        [
                            'sport_id' => $sport->id,
                            'tarih' => $date->format('Y-m-d'),
                            'saat' => $slotTime,
                        ],
                        [
                            'kapasite' => 1,
                            'rezervasyon_sayisi' => 0
                        ]
                    );
                    $createdCount++;

                    $currentHour = $nextHour;
                     if($currentHour->equalTo(Carbon::createFromTime(0, 0, 0))) break;
                }
            }
        }
        
        if ($createdCount > 0) {
            Log::info("{$createdCount} adet yeni slot oluşturuldu.");
        }
    }
}