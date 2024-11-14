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
        'stock',
        'unit_id',
        'branch_id'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }

    public function inbounds()
    {
        return $this->hasMany(InboundItem::class);
    }

    public function outbounds()
    {
        return $this->hasMany(OutboundItem::class);
    }
}
