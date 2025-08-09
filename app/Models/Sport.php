<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = [
        'ad',
        'resim',
        'notes',
        'is_active', // YENİ
        'status_reason', // YENİ
        'location_coordinates' // YENİ
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    //... kalan kodlar
}