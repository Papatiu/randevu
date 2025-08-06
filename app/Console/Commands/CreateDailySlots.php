<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\Sport;
use App\Models\Slot;

class CreateDailySlots extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:create-daily-slots';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Creates new available slots for the upcoming 7th day.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Günlük slot oluşturma işlemi başlatılıyor...');

        // 7 gün sonrası için tarih belirleniyor.
        $targetDate = Carbon::now()->addDays(7)->format('Y-m-d');
        $this->info("Hedef tarih: {$targetDate}");

        // Bu tarih için daha önce slot oluşturulmuş mu diye kontrol edelim.
        $existingSlots = Slot::whereDate('tarih', $targetDate)->count();
        if ($existingSlots > 0) {
            $this->warn("{$targetDate} tarihi için slotlar zaten mevcut. İşlem atlanıyor.");
            return;
        }

        $sports = Sport::all();
        // Randevuya açık olan saatler
        $hours = ['18:00', '19:00', '20:00', '21:00', '22:00', '23:00'];
        $createdCount = 0;

        foreach ($sports as $sport) {
            foreach ($hours as $hour) {
                Slot::create([
                    'sport_id' => $sport->id,
                    'tarih' => $targetDate,
                    'saat' => $hour,
                    'kapasite' => 1,
                    'rezervasyon_sayisi' => 0,
                ]);
                $createdCount++;
            }
        }
        
        $this->info("{$targetDate} tarihi için toplam {$createdCount} adet yeni slot başarıyla oluşturuldu.");
    }
}