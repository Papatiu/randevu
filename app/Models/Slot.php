<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

}
