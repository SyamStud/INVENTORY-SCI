<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outbound extends Model
{
    protected $fillable = [
        'outbound_number',
        'release_to',
        'release_reason',
        'request_note_number',
        'delivery_note_number',
        'date_released',
        'approved_by',
        'released_by',
        'received_by',
        'total_price',
        'photo',
        'document_path',
        'status',
        'branch_id',
        'user_id',
    ];

    public function branch()
    {
        return $this->belongsTo(BranchOffice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function releasedBy()
    {
        return $this->belongsTo(Employee::class, 'released_by');
    }

    public function items()
    {
        return $this->hasMany(OutboundItem::class);
    }

    public function signatures()
    {
        return $this->morphMany(Signature::class, 'signable');
    }
}
