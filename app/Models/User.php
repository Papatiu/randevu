<?php

namespace App\Models;

// ...
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // ... (diğer kodlar aynı kalacak) ...

    protected $fillable = [
        'tc_kimlik',
        'ad',
        'soyad',
        'adres',
        'telefon',
        'dogum_tarihi',
        'email',
        'password',
        'is_admin', // Bunu ekle
    ];

    // ...

    /**
     * name özelliği çağrıldığında ad ve soyadı birleştirip döndürür.
     * BU FONKSİYONU EKLE
     */
    public function getNameAttribute()
    {
        return "{$this->ad} {$this->soyad}";
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}