<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Sport;
use App\Models\Slot;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Önceki verileri temizleyelim ki her seferinde üstüne yazmasın
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Reservation::truncate();
        Slot::truncate();
        User::truncate();
        Sport::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Spor Dalları Oluşturulsun
        $this->call(SportSeeder::class);
        $this->command->info('Spor dalları oluşturuldu.');

        // 2. Test Kullanıcıları Oluşturalım
        $userTony = User::create([
            'ad' => 'Tony', 'soyad' => 'Stark', 'tc_kimlik' => '11111111111', 'telefon' => '5551112233',
            'adres' => 'Stark Kulesi, New York', 'dogum_tarihi' => '1970-05-29',
            'email' => 'tony@stark.com', 'password' => Hash::make('password'),
        ]);

        $userSteve = User::create([
            'ad' => 'Steve', 'soyad' => 'Rogers', 'tc_kimlik' => '22222222222', 'telefon' => '5554445566',
            'adres' => 'Brooklyn, New York', 'dogum_tarihi' => '1918-07-04',
            'email' => 'steve@avengers.com', 'password' => Hash::make('password'),
        ]);
        $this->command->info('Test kullanıcıları oluşturuldu (tony@stark.com, steve@avengers.com - şifre: password).');

        // 3. Slotları (Tüm Boş Saatleri) Oluşturalım
        $sports = Sport::all();
        $hours = ['18:00', '19:00', '20:00', '21:00', '22:00', '23:00'];
        $allCreatedSlots = [];

        foreach ($sports as $sport) {
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::now()->addDays($i);
                foreach ($hours as $hour) {
                    $allCreatedSlots[] = Slot::create([
                        'sport_id' => $sport->id,
                        'tarih' => $date->format('Y-m-d'),
                        'saat' => $hour,
                        'kapasite' => 1,
                        'rezervasyon_sayisi' => 0
                    ]);
                }
            }
        }
        $this->command->info('Tüm spor dalları için 7 günlük slotlar oluşturuldu.');

        // 4. ÖRNEK REZERVASYONLARI OLUŞTURALIM!
        // Senaryo: Tenis kortu yarın 19:00 ve 20:00'de dolsun. Halı Saha 1 de bugün 21:00'de.

        // Tenis için (ID'si 1 olduğunu varsayıyoruz)
        $tenisSportId = 1;
        $yarin = Carbon::now()->addDay()->format('Y-m-d');
        
        // Yarın 19:00 slotunu bul ve Tony'ye rezerve et
        $slot1 = Slot::where('sport_id', $tenisSportId)->where('tarih', $yarin)->where('saat', '19:00')->first();
        if($slot1) {
            Reservation::create(['user_id' => $userTony->id, 'slot_id' => $slot1->id]);
            $slot1->increment('rezervasyon_sayisi');
        }

        // Yarın 20:00 slotunu bul ve Steve'e rezerve et
        $slot2 = Slot::where('sport_id', $tenisSportId)->where('tarih', $yarin)->where('saat', '20:00')->first();
         if($slot2) {
            Reservation::create(['user_id' => $userSteve->id, 'slot_id' => $slot2->id]);
            $slot2->increment('rezervasyon_sayisi');
        }

        // Halı Saha 1 için (ID'si 2 olduğunu varsayıyoruz)
        $haliSahaSportId = 2;
        $bugun = Carbon::now()->format('Y-m-d');

        // Bugün 21:00 slotunu bul ve Tony'ye rezerve et
        $slot3 = Slot::where('sport_id', $haliSahaSportId)->where('tarih', $bugun)->where('saat', '21:00')->first();
        if($slot3) {
            Reservation::create(['user_id' => $userTony->id, 'slot_id' => $slot3->id]);
            $slot3->increment('rezervasyon_sayisi');
        }

        $this->command->info('Örnek rezervasyonlar oluşturuldu ve slot sayıları güncellendi.');
        $this->command->info('Veritabanı başarıyla hazırlandı!');
    }
}