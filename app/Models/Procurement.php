<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procurement extends Model
{
    protected $fillable = [
        'entry_date',
        'name',
        'user_name',
        'status',
        'process_date',
        'invoice_status',
        'invoice_date',
        'payment_status',
        'payment_date',
        'branch_id',
    ];
}
