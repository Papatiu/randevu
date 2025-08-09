<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sport_id',
        'tarih',
        'saat',
        'kapasite',
        'rezervasyon_sayisi',
    ];


    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function reservations()
    {
        // Not: Artık 'reservations' yerine 'appointments' kullanıyoruz.
        // Bu fonksiyonu güncelleyebilir veya silebiliriz. Şimdilik kalsın.
        return $this->hasMany(Appointment::class); // Bunu Appointment olarak güncelleyelim
    }
}