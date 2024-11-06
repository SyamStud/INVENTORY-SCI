<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
       // 'category_id',
        'unit_id',
        'branch_id'
    ];

    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }
}
