<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundItem extends Model
{
    protected $fillable = [
        'outbound_temp_id',
        'item_id',
        'quantity',
        'price',
        'total_price',
        'branch_id',
    ];

    public function outbound()
    {
        return $this->belongsTo(Outbound::class);
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
