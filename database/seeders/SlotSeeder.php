<?php

// database/seeders/SlotSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Slot;
use Illuminate\Support\Carbon;

class SlotSeeder extends Seeder
{
    public function run()
    {
        $sport_ids = [1, 2, 3, 4, 5]; // örnek
        $hours = ['10:00', '11:00', '12:00', '13:00', '14:00'];

        foreach ($sport_ids as $sport_id) {
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::now()->addDays($i)->format('Y-m-d');
                foreach ($hours as $hour) {
                    Slot::create([
                        'sport_id' => $sport_id,
                        'tarih' => $date,
                        'saat' => $hour,
                        'rezervasyon_sayisi' => 0
                    ]);
                }
            }
        }
    }
}
