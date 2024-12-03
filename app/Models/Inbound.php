<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inbound extends Model
{
    protected $fillable = [
        'po_number',
        'bpg_number',
        'order_note_number',
        'date',
        'received_by',
        'total_cost',
        'branch_id',
        'user_id',
    ];

    public function items()
    {
        return $this->hasMany(InboundItem::class);
    }

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(Employee::class, 'received_by');
    }
}
