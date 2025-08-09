<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannedTc extends Model
{
    use HasFactory;

    protected $table = 'banned_tcs';

    protected $fillable = [
        'tc_kimlik',
        'ban_bitis_tarihi',
        'sebep',
    ];

    protected $casts = [
        'ban_bitis_tarihi' => 'datetime',
    ];
}