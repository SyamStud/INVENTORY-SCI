<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundTemp extends Model
{
    protected $fillable = [
        'inbound_number',
        'received_from',
        'order_note_number',
        'contract_note_number',
        'delivery_note_number',
        'date_received',
        'received_by',
        'total_cost',
        'branch_id',
        'user_id',
    ];

    public function items()
    {
        return $this->hasMany(InboundItemTemp::class);
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
