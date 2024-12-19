<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleUsage extends Model
{
    protected $fillable = [
        'vehicle_id',
        'date',
        'employee_id',
        'time_start',
        'time_end',
        'purpose',
        'driver',
        'branch_id',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch()
    {
        return $this->belongsTo(BranchOffice::class);
    }
}
