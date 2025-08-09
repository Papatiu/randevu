<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Sport;
use App\Models\Slot;
use App\Models\Appointment;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Sport Seeder (8 adet spor eklemek için SportSeeder.php içeriğini değiştiriyoruz)
        // Yeni Spor Seeder'ı çağıralım. (Aşağıdaki SportSeeder içeriğini de kontrol et)
        $this->call(SportSeeder::class);
        $this->command->info("Spor dalları oluşturuldu.");

        // 2. Test Kullanıcıları Oluştur
        // ... (Bu kısım öncekiyle aynı) ...
        $user1 = User::firstOrCreate(
            ['email' => 'tony@stark.com'],
            [
                'ad' => 'Tony',
                'soyad' => 'Stark',
                'tc_kimlik' => '11111111111',
                'telefon' => '5551112233',
                'adres' => 'Stark Kulesi, New York',
                'dogum_tarihi' => '1970-05-29',
                'password' => Hash::make('password'),
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'steve@avengers.com'],
            [
                'ad' => 'Steve',
                'soyad' => 'Rogers',
                'tc_kimlik' => '22222222222',
                'telefon' => '5554445566',
                'adres' => 'Brooklyn, New York',
                'dogum_tarihi' => '1918-07-04',
                'password' => Hash::make('password'),
            ]
        );
        $this->command->info("Test kullanıcıları oluşturuldu (tony@stark.com, steve@avengers.com - şifre: password).");

        // 3. Slotları Oluştur (Yeni format HH:MM - HH:MM)
        $sports = Sport::all();
        $startHour = Carbon::createFromTime(18, 0, 0); // Başlangıç saati: 18:00
        $endHour = Carbon::createFromTime(23, 59, 0);   // Bitiş saati: 23:59 (Dediğin gibi 00:00'a kadar)
        $slots = [];

        foreach ($sports as $sport) {
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::now()->addDays($i)->format('Y-m-d');
                $currentHour = clone $startHour;

                while ($currentHour->lessThanOrEqualTo($endHour)) {
                    $nextHour = clone $currentHour;
                    $nextHour->addHour();
                    
                    // Saati "18:00 - 19:00" formatında kaydediyoruz
                    $slotTime = $currentHour->format('H:i') . ' - ' . $nextHour->format('H:i');

                    $slot = Slot::create([
                        'sport_id' => $sport->id,
                        'tarih' => $date,
                        'saat' => $slotTime,
                        'kapasite' => 1,
                        'rezervasyon_sayisi' => 0
                    ]);
                    $slots[] = $slot;
                    
                    // Saati bir saat ilerlet
                    $currentHour = $nextHour;
                    if($currentHour->equalTo(Carbon::createFromTime(0, 0, 0))) {
                        // 00:00'ı da dahil ettikten sonra döngüyü sonlandır
                        break;
                    }
                }
            }
        }
        $this->command->info("Tüm spor dalları için 7 günlük slotlar (HH:MM - HH:MM formatında) oluşturuldu.");

        // 4. Rastgele Rezervasyonlar Yapalım
        $slotsToReserveCount = floor(count($slots) * 0.4); 
        $slotsToReserve = collect($slots)->shuffle()->take($slotsToReserveCount);
        
        $users = [$user1, $user2];
        $reservationCount = 0;

        foreach($slotsToReserve as $slot) {
            $randomUser = $users[array_rand($users)];

            if ($slot->rezervasyon_sayisi < $slot->kapasite) {
                Appointment::create([
                    'slot_id' => $slot->id,
                    'tc_kimlik' => $randomUser->tc_kimlik,
                    'ad' => $randomUser->ad,
                    'soyad' => $randomUser->soyad,
                    'dogum_yili' => Carbon::parse($randomUser->dogum_tarihi)->year,
                    'telefon' => $randomUser->telefon,
                    'iptal_kodu' => strtoupper(Str::random(8)),
                    'durum' => 'onaylandi', 
                ]);

                $slot->increment('rezervasyon_sayisi');
                $reservationCount++;
            }
        }
        $this->command->info("{$reservationCount} adet rastgele onaylı randevu oluşturuldu.");
    }
}