<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    protected $fillable = [
        'name',
        'number',
        'institution',
        'due_date',
        'branch_id',
        'file',
        'is_reminded',
        'last_reminded_at'
    ];
}
