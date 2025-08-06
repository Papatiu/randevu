<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sport;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            ['ad' => 'Tenis', 'resim' => 'tenis.jpg'],
            ['ad' => 'Halı Saha 1', 'resim' => 'hali1.jpg'],
            ['ad' => 'Halı Saha 2', 'resim' => 'hali2.jpg'],
            ['ad' => 'Voleybol', 'resim' => 'voleybol.jpg'],
            ['ad' => 'Basketbol', 'resim' => 'basketbol.jpg'],
        ];

        foreach ($sports as $sport) {
            Sport::create($sport);
        }
    }
}
