<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'npp',
        'name',
        'position_id',
        'branch_id',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class)->withTrashed();
    }

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
