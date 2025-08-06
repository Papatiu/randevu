<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

}
