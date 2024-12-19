<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryIn extends Model
{
    protected $table = 'delivery_ins';
    protected $fillable = [
        'date',
        'sender',
        'receiver',
        'received_date',
        'received_by',
        'photo',
        'branch_id',
    ];
}
