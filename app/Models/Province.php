<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public function regencies()
    {
        return $this->hasMany(Regency::class);
    }
}
