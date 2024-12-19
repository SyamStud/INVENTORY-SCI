<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'nopol',
        'brand',
        'stnk',
        'kir',
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(BranchOffice::class);
    }
}
