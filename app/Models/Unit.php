<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'branch_id'
    ];

    public function branch()
    {
        return $this->belongsTo(BranchOffice::class);
    }
}
