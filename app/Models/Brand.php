<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'branch_id'
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }
}
