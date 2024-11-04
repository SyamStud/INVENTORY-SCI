<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'branch_id',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class)->withTrashed();
    }

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }
}
