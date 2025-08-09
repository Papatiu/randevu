<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_id',
        'tc_kimlik',
        'ad',
        'soyad',
        'dogum_yili',
        'telefon',
        'iptal_kodu',
        'durum',
        'user_id',
        'aciklama',
        'team_name',
        'participant_count',
    ];


    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Admin ilişkisi için
    }
}