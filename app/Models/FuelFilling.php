<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelFilling extends Model
{
    protected $fillable = [
        'vehicle_id',
        'km_fillings',
        'quantity',
        'driver',
        'branch_id',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function branch()
    {
        return $this->belongsTo(BranchOffice::class);
    }
}
