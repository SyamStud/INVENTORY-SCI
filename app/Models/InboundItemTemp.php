<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundItemTemp extends Model
{
    protected $fillable = [
        'inbound_temp_id',
        'item_id',
        'quantity',
        'cost',
        'total_cost',
        'branch_id',
    ];

    public function inboundTemp()
    {
        return $this->belongsTo(InboundTemp::class);
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
