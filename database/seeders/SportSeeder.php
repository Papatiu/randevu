<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sport;
use Schema;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        // Önce kısıtlamaları geçici olarak devre dışı bırak
        Schema::disableForeignKeyConstraints();

        // Şimdi tabloyu güvenle boşaltabiliriz
        Sport::truncate();

        // Kısıtlamaları tekrar aktif et
        Schema::enableForeignKeyConstraints();
        
        $sports = [
            ['ad' => 'Tenis 1', 'resim' => 'tenis.webp'],
            ['ad' => 'Tenis 2', 'resim' => 'tenis.webp'],
            ['ad' => 'Halı Saha 1', 'resim' => 'halısaha1.jpg'],
            ['ad' => 'Halı Saha 2', 'resim' => 'halısaha-2.webp'],
            ['ad' => 'Voleybol 1', 'resim' => 'voleybol.jpg'],
            ['ad' => 'VoleyBol 2', 'resim' => 'voleybol.jpgg'],
            ['ad' => 'Basketbol 1', 'resim' => 'basketbol.jpg'],
            ['ad' => 'Basketbol 2', 'resim' => 'basketbol.jpg'],
        ];

        foreach ($sports as $sport) {
            Sport::create($sport);
        }
    }
}