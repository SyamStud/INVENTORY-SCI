<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchOffice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class)->withTrashed();
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}
