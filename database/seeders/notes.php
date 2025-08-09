<?php

// ...
use App\Models\Note; 
use Database\Seeders\SportSeeder;
use Illuminate\Database\Seeder;// en üste ekle

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SportSeeder::class);
        $this->command->info("Spor dalları oluşturuldu.");
        
        // --- YENİ EKLENEN NOTLAR BÖLÜMÜ ---
        Note::truncate(); // Önceki notları temizle
        Note::create([
            'content' => '<p><strong>Kural 1:</strong> Tesislere girerken galoş giymek zorunludur.</p>',
            'is_active' => true,
            'order' => 1
        ]);
        Note::create([
            'content' => '<p><strong>Kural 2:</strong> Randevu saatinizden 15 dakika önce tesiste olmanız gerekmektedir.</p>',
            'is_active' => true,
            'order' => 2
        ]);
        $this->command->info("Örnek notlar ve kurallar eklendi.");
        // ------------------------------------

        // ... (kalan seeder kodu: kullanıcılar, slotlar, randevular)
    }
}