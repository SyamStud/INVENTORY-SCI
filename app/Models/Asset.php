<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'inventory_number',
        'name',
        'serial_number',
        'brand',
        'calibration',
        'photo',
        'branch_id',
    ];

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }
}
