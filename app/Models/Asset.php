<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'inventory_number',
        'name',
        'serial_number',
        'brand_id',
        'calibration',
        'photo',
        'branch_id',
        'tag_number',
        'color',
        'condition',
        'status',
        'calibration_number',
        'calibration_interval',
        'calibration_start_date',
        'calibration_due_date',
        'calibration_institution',
        'calibration_type',
        'range',
        'correction_factor',
        'significance',
        'size',
        'procurement'
    ];

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
