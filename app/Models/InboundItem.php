<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundItem extends Model
{
    protected $fillable = [
        'inbound_id',
        'item_id',
        'quantity',
        'cost',
        'total_cost',
        'branch_id',
    ];

    public function inbound()
    {
        return $this->belongsTo(Inbound::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }
}
