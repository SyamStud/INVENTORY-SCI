<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOut extends Model
{
    protected $table = 'delivery_outs';
    protected $fillable = [
        'resi',
        'delivery_date',
        'sender',
        'receiver',
        'received_date',
        'received_by',
        'photo',
        'branch_id',
    ];
}
